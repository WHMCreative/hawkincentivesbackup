<?php

namespace Drupal\e3_marketo_bhk\Plugin\MarketoHandler;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\State\StateInterface;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\e3_marketo\Annotation\MarketoHandler;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;
use Drupal\e3_marketo\Plugin\MarketoHandler\DefaultMarketoHandler;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Extra BHK handler for Marketo Forms 2.0.
 *
 * @MarketoHandler(
 *   id = "bhk_marketo_handler",
 *   label = @Translation("BHK Marketo Handler"),
 *   description = @Translation("Marketo handler extended for BHK-specific functionality requirements."),
 *   bundles = {},
 *   priority = 70
 * )
 */
class BhkMarketoHandler extends DefaultMarketoHandler {

  /**
   * Parent Reference Component.
   *
   * @var \Drupal\paragraphs\ParagraphInterface
   */
  protected $parentComponent;

  /**
   * TRUE if the form is Gated.
   *
   * @var boolean
   */
  protected $gated;

  /**
   * State Storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Request Stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var static $handler */
    $handler = parent::create($container, $configuration, $plugin_id, $plugin_definition);

    $handler->injectAdditionalServices(
      $container->get('state'),
      $container->get('request_stack')
    );

    return $handler;
  }

  /**
   * Inject additional services, not specified within the main form.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   State Storage.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request Stack.
   */
  public function injectAdditionalServices(StateInterface $state, RequestStack $request_stack) {
    $this->state = $state;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public function embedMarketoForm(MarketoFormEntityInterface $marketo_form, array &$build) {
    parent::embedMarketoForm($marketo_form, $build);

    if (isset($build['marketo_form_embed'])) {
      $build['marketo_form_embed']['#attached']['library'][] = 'e3_marketo_bhk/bhk-marketo-forms';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function alterScriptParameters(&$params, MarketoFormEntityInterface $marketo_form) {

    $params[$this->instance]['submissionCallbacks'] = $this->getSubmissionCallbacks();

    $parent = $this->parentComponent;

    /**
     * Set parameters for redirect on submission.
     *
     * This functionality is implemented separately for Gated Content.
     */
    if ($parent->hasField('field_thank_you_url') && !$parent->get('field_thank_you_url')->isEmpty()) {

      try {
        /** @var \Drupal\Core\Url $url */
        $url = $parent->get('field_thank_you_url')->first()->getUrl();
        $params[$this->instance]['redirectPath'] = $url->toString();
      } catch (MissingDataException $e) {
        // TODO - add log entry on error?
      }
    }

    return $params;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(MarketoFormEntityInterface $marketo_form, $callback, &$arguments = NULL) {
    $result = parent::applies($marketo_form, $callback, $arguments);

    // Do not apply if this is a gated form.
    if (isset($arguments['#gated'])) {
      $this->gated = TRUE;
    }
    else {
      $this->gated = FALSE;
    }

    // Verify that all prerequisites are set correctly for the plugin to work.
    if (!empty($marketo_form->_referringItem)) {

      /** @var \Drupal\paragraphs\ParagraphInterface $reference_paragraph */
      $reference_paragraph = $marketo_form->_referringItem->getEntity();
      if ($reference_paragraph instanceof ParagraphInterface) {

        // Save the parent paragraph for use in other plugin methods.
        $this->parentComponent = $reference_paragraph;
        return $result;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubmissionCallbacks() {

    // Determine which submission behavior to apply.
    $parent = $this->parentComponent;
    if ($parent->hasField('field_submission_behavior')) {
      $sub_behavior = $parent->get('field_submission_behavior')->value;
    }

    // Respect the gated functionality.
    if ($this->gated) {
      return [
        $this->getPluginId() => 'unlockGatedContent',
      ];
    }

    // Fallback for no submission behavior set.
    if (empty($sub_behavior)) {
      return parent::getSubmissionCallbacks();
    }

    switch ($sub_behavior) {
      // Redirect user to specified url and add query parameters, if provided.
      case 'redirect':
        return [
          $this->getPluginId() => 'redirectUser',
        ];
      case 'marketo':
        return [];
      case 'confirmation':
      default:
        return [
          $this->getPluginId() => 'submitMarketoForm',
        ];
    }
  }

}
