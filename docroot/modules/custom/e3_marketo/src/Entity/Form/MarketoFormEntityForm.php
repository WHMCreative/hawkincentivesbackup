<?php

namespace Drupal\e3_marketo\Entity\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\e3_marketo\Plugin\MarketoHandlerManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Marketo form entity edit forms.
 *
 * @ingroup e3_marketo
 */
class MarketoFormEntityForm extends ContentEntityForm {

  /**
   * Marketo Handlers Manager.
   *
   * @var \Drupal\e3_marketo\Plugin\MarketoHandlerManager
   */
  protected $marketoHandlerManager;

  /**
   * Constructs a MarketoFormEntityForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\e3_marketo\Plugin\MarketoHandlerManager $marketo_handler_manager
   *   Marketo handler manager service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(EntityManagerInterface $entity_manager, MarketoHandlerManager $marketo_handler_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL) {
    parent::__construct($entity_manager, $entity_type_bundle_info, $time);

    $this->marketoHandlerManager = $marketo_handler_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('plugin.manager.marketo_handler_manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\e3_marketo\Entity\MarketoFormEntity */
    $form = parent::buildForm($form, $form_state);
    $operation = $this->operation;

    $form['#title'] = $this->t('@operation @title Marketo Form', [
      '@operation' => ucfirst($operation),
      '@title' => $operation === 'add' ? $this->getBundleEntity()->label() : $this->entity->getName(),
    ]);

    if (!$this->entity->isNew()) {
      $form['new_revision'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => FALSE,
        '#weight' => 10,
      ];

      $form['revision_log_message']['#states'] = [
        'visible' => [
          'input[name="new_revision"]' => ['checked' => TRUE],
        ]
      ];
    }
    else {
      $form['revision_log_message']['#access'] = FALSE;
    }

    // Invoke all alterMarketoEntityForm callbacks on applicable handlers.
    $this->marketoHandlerManager->invokeHandlers($this->entity, 'alterMarketoEntityForm', $form);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(REQUEST_TIME);
      $entity->setRevisionUserId($this->currentUser()->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the @label Marketo form entity.', [
          '@label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the @label Marketo form entity.', [
          '@label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.marketo_form.canonical', ['marketo_form' => $entity->id()]);
  }

}
