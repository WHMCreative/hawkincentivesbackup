<?php

namespace Drupal\e3_gated_content\Plugin\MarketoHandler;

use Drupal\Core\Annotation\Translation;
use Drupal\e3_marketo\Annotation\MarketoHandler;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;
use Drupal\e3_marketo\Plugin\MarketoHandler\DefaultMarketoHandler;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Default handler to build output for Marketo Forms.
 *
 * @MarketoHandler(
 *   id = "gated_content_marketo_handler",
 *   label = @Translation("Gated Content Marketo Handler"),
 *   description = @Translation("Handler to allow Marketo Forms to unlock Gated Content."),
 *   bundles = {},
 *   priority = 90
 * )
 */
class GatedContentHandler extends DefaultMarketoHandler {

  /**
   * Content types with the gated functionality enabled.
   *
   * @var array
   */
  protected $gatedContentTypes = [
    'insight',
  ];

  /**
   * Gated Node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $parentNode = NULL;

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
      $container->get('request_stack')
    );

    return $handler;
  }

  /**
   * Inject additional services, not specified within the main form.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request Stack.
   */
  public function injectAdditionalServices(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public function embedMarketoForm(MarketoFormEntityInterface $marketo_form, array &$build) {
    parent::embedMarketoForm($marketo_form, $build);

    if (isset($build['marketo_form_embed'])) {
      $build['marketo_form_embed']['#attached']['library'][] = 'e3_gated_content/gated-content-unlocker';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function alterScriptParameters(&$params, MarketoFormEntityInterface $marketo_form) {
    $params[$this->instance]['submissionCallbacks'] = $this->getSubmissionCallbacks();
    $params[$this->instance]['gatedContent'] = $this->parentNode->id();
  }

  /**
   * {@inheritdoc}
   */
  public function applies(MarketoFormEntityInterface $marketo_form, $callback, &$arguments = NULL) {
    $result = parent::applies($marketo_form, $callback, $arguments);

    // Make sure that gated content should be applied.
    if (!empty($marketo_form->_referringItem)) {
      /** @var ParagraphInterface $reference_paragraph */
      $reference_paragraph = $marketo_form->_referringItem->getEntity();

      if ($reference_paragraph instanceof ParagraphInterface) {
        $parent_node = $reference_paragraph->getParentEntity();

        if (!$parent_node) {
          return FALSE;
        }

        if ($parent_node->hasField('field_gated') && !$parent_node->get('field_gated')->value) {
          return FALSE;
        }

        // Check if current node is within the list of gated. Save it for the
        // use in other callbacks to speed up performance.
        if (in_array($parent_node->bundle(), $this->gatedContentTypes)) {
          $this->parentNode = $parent_node;
          $arguments['#gated'] = TRUE;
          return $result;
        }
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubmissionCallbacks() {
    return [
      $this->getPluginId() => 'unlockGatedContent',
    ];
  }
}
