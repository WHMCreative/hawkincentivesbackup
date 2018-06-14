<?php

namespace Drupal\e3_marketo\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;

/**
 * Class MarketoFormEntityController.
 *
 * Returns responses for Marketo form entity routes.
 *
 * @package Drupal\e3_marketo\Controller
 */
class MarketoFormEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  public $renderer;

  /**
   * Date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  public $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public function __construct(Renderer $renderer, DateFormatterInterface $date_formatter) {
    $this->renderer = $renderer;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('date.formatter')
    );
  }

  /**
   * Displays a Marketo form entity  revision.
   *
   * @param int $marketo_form_revision
   *   The Marketo form entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function revisionShow($marketo_form_revision) {
    $marketo_form = $this->entityTypeManager()->getStorage('marketo_form')->loadRevision($marketo_form_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('marketo_form');

    return $view_builder->view($marketo_form);
  }

  /**
   * Page title callback for a Marketo form entity  revision.
   *
   * @param int $marketo_form_revision
   *   The Marketo form entity  revision ID.
   *
   * @return string
   *   The page title.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function revisionPageTitle($marketo_form_revision) {
    $marketo_form = $this->entityTypeManager()->getStorage('marketo_form')->loadRevision($marketo_form_revision);
    return $this->t('Revision of %title from %date', ['%title' => $marketo_form->label(), '%date' => $this->dateFormatter->format($marketo_form->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Marketo form entity .
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   A Marketo form entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function revisionOverview(MarketoFormEntityInterface $marketo_form) {
    $account = $this->currentUser();
    $langcode = $marketo_form->language()->getId();
    $langname = $marketo_form->language()->getName();
    $languages = $marketo_form->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $marketo_form_storage = $this->entityTypeManager()->getStorage('marketo_form');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $marketo_form->label()]) : $this->t('Revisions for %title', ['%title' => $marketo_form->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all marketo form entity revisions") || $account->hasPermission('administer marketo form entity entities')));
    $delete_permission = (($account->hasPermission("delete all marketo form entity revisions") || $account->hasPermission('administer marketo form entity entities')));

    $rows = [];

    $vids = $marketo_form_storage->revisionIds($marketo_form);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $revision */
      $revision = $marketo_form_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $marketo_form->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.marketo_form.revision', ['marketo_form' => $marketo_form->id(), 'marketo_form_revision' => $vid]));
        }
        else {
          $link = $marketo_form->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#type' => 'html_tag',
              '#tag' => 'em',
              '#value' => $this->t('Current revision'),
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.marketo_form.translation_revert', ['marketo_form' => $marketo_form->id(), 'marketo_form_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.marketo_form.revision_revert', ['marketo_form' => $marketo_form->id(), 'marketo_form_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.marketo_form.revision_delete', ['marketo_form' => $marketo_form->id(), 'marketo_form_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['marketo_form_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

  /**
   * Ajax callback to get a rendered Marketo thank you page component.
   *
   * @param Request $request
   *   Current request object.
   *
   * @return JsonResponse
   *   Modal response.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function getThankYouPage(Request $request){
    // Return if error occurred.
    if (!$request->request->count()) {
      return new JsonResponse(FALSE);
    }

    $params = $request->request->all();

    // Load in, render and prepare marketo thank you page component for the form.
    $marketo_entity = $this->entityTypeManager()->getStorage('marketo_form')->load($params['id']);
    $marketo_entity_view = $this->entityTypeManager()->getViewBuilder('marketo_form')->view($marketo_entity, 'thank_you_page');

    $rendered_entity = $this->renderer->render($marketo_entity_view);
    
    return new JsonResponse([
      'rendered_entity' => $rendered_entity
    ]);
  }

}
