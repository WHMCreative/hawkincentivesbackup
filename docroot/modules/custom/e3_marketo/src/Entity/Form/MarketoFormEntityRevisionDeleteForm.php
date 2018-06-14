<?php

namespace Drupal\e3_marketo\Entity\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Marketo form entity revision.
 *
 * @ingroup e3_marketo
 */
class MarketoFormEntityRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Marketo form entity revision.
   *
   * @var \Drupal\e3_marketo\Entity\MarketoFormEntityInterface
   */
  protected $revision;

  /**
   * The Marketo form entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $marketoFormEntityStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  public $dateFormatter;

  /**
   * Constructs a new MarketoFormEntityRevisionDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $entity_storage, Connection $connection, DateFormatterInterface $date_formatter) {
    $this->marketoFormEntityStorage = $entity_storage;
    $this->connection = $connection;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('marketo_form'),
      $container->get('database'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'marketo_form_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', ['%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime())]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.marketo_form.version_history', ['marketo_form' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $marketo_form_revision = NULL) {
    $this->revision = $this->marketoFormEntityStorage->loadRevision($marketo_form_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $revision */
    $revision = $this->revision;
    $this->marketoFormEntityStorage->deleteRevision($revision->getRevisionId());

    $this->logger('content')->notice('Marketo form entity: deleted %title revision %revision.', ['%title' => $revision->label(), '%revision' => $revision->getRevisionId()]);
    $this->messenger()->addStatus($this->t('Revision from %revision-date of Marketo form entity %title has been deleted.', ['%revision-date' => $this->dateFormatter->format($revision->getRevisionCreationTime()), '%title' => $revision->label()]));
    $form_state->setRedirect(
      'entity.marketo_form.canonical',
       ['marketo_form' => $revision->id()]
    );

    $count_query = $this->connection->select('marketo_form_field_revision', 'mfr')
      ->fields('mfr', ['vid'])
      ->condition('mfr.id', $revision->id())
      ->distinct(TRUE)
      ->countQuery()
      ->execute()
      ->fetchField();

    if ($count_query > 1) {
      $form_state->setRedirect(
        'entity.marketo_form.version_history',
         ['marketo_form' => $revision->id()]
      );
    }
  }

}
