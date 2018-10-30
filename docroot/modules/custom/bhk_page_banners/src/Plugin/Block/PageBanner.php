<?php

namespace Drupal\bhk_page_banners\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\TitleResolver;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityTypeManager;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'PageBanner' block.
 *
 * @Block(
 *  id = "page_banner",
 *  admin_label = @Translation("Page Banner"),
 * )
 */
class PageBanner extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Controller\TitleResolver definition.
   *
   * @var \Drupal\Core\Controller\TitleResolver
   */
  protected $titleResolver;

  /**
   * Drupal\Core\Language\LanguageManager definition.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Symfony\Component\HttpFoundation\Request definition.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Controller\TitleResolver $title_resolver
   *   The title resolver service.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager service.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The route match service.
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager service.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The entity type manager service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    TitleResolver $title_resolver,
    LanguageManager $language_manager,
    CurrentRouteMatch $current_route_match,
    EntityTypeManager $entity_type_manager,
    Request $request
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->titleResolver = $title_resolver;
    $this->languageManager = $language_manager;
    $this->routeMatch = $current_route_match;
    $this->entityTypeManager = $entity_type_manager;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('title_resolver'),
      $container->get('language_manager'),
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
      $container->get('request_stack')->getCurrentRequest()
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $access = parent::blockAccess($account);

    // Deny access if there is no node in the route to avoid errors.
    $current_node = $this->routeMatch->getParameter('node');
    if (!$current_node) {
      return AccessResult::forbidden();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Check if this is a revision route first. If it is, load corresponding
    // revision.
    $current_revision = $this->routeMatch->getParameter('node_revision');

    if ($current_revision && !is_object($current_revision)) {
      $current_revision = $this->entityTypeManager->getStorage('node')->loadRevision($current_revision);
    }

    if ($current_revision) {
      $current_node = $current_revision;
    }
    else {

      $current_node = $this->routeMatch->getParameter('node');

      if ($current_node && !is_object($current_node)) {
        $node_storage = $this->entityTypeManager->getStorage('node');
        $current_node = $node_storage->load($current_node);
      }
    }

    $header_render_array = [];

    $lang_code = $this->languageManager->getCurrentLanguage()->getId();

    if ($current_node && $current_node->hasTranslation($lang_code)) {
      $node_current_translation = $current_node->getTranslation($lang_code);
    }
    else {
      // If there is no translation of the node, end and return an empty array.
      $node_current_translation = [];
    }

    // If current page is a node, test if it has the paragraph header field.
    if ($node_current_translation && $node_current_translation->hasField('field_p_header') && !$node_current_translation->field_p_header->isEmpty()) {
      // Build field_p_header.
      $header_field = $node_current_translation->field_p_header;
      $viewBuilder = $this->entityTypeManager->getViewBuilder('node');
      $header_render_array = $viewBuilder->viewField($header_field, ['label' => 'hidden']);
      $paragraph = NULL;
      if (isset($header_render_array[0]['#paragraph'])) {
        $paragraph = $header_render_array[0]['#paragraph'];
      }

      if ($paragraph->hasField('field_heading') && empty($paragraph->field_heading->getValue())) {
        $paragraph->field_heading->setValue($node_current_translation->getTitle());
      }

      if ($paragraph->hasField('field_large_heading') && empty($paragraph->field_large_heading->getValue())) {
        $paragraph->field_large_heading->setValue($node_current_translation->getTitle());
      }
    }
    elseif ($node_current_translation) {
      $header_render_array = ['#markup' => '<div class="header-banner-plain"><div class="header-banner-plain__content"><h1>' . $node_current_translation->getTitle() . '</h1></div></div>'];
    }
    else {
      // @TODO: need to check this piece for translated content
      $request = $this->request;
      $route = $this->routeMatch->getRouteObject();
      $title = '';
      $webform = $this->routeMatch->getParameter('webform');

      if ($request && $route) {
        $title = $this->titleResolver->getTitle($request, $route);
      }

      // Test if there is a webform item in the route, if so, use title if set.
      if (is_object($webform)) {
        // The line below causes this block to be blank on all webform pages.
        // It's not clear that we still need this block, but this return []
        // will disable it on webform pages where it was causing a double title
        // until we sort that out.
        return [];

        /* $conf_title = $webform->getSetting('confirmation_title');
        if (!empty($conf_title)) {
        $title = $conf_title;
        } */
      }

      if (is_array($title)) {
        $title = render($title);
      }
      $header_render_array = [
        '#type' => 'container',
        '#attributes' => [
          'class' => 'header-banner-plain',
        ],
        'header_content' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => 'header-banner-plain__content',
          ],
          'header_title' => [
            '#type' => 'html_tag',
            '#tag' => 'h1',
            '#value' => render($title),
          ],
        ],
      ];
    }

    $build = [
      '#title' => '',
      'content' => [
        'header' => $header_render_array,
      ],
    ];

    return $build;
  }

  /**
   * Define cache tags for custom block.
   */
  public function getCacheTags() {
    // With this when your node change your block will rebuild.
    if ($node = $this->routeMatch->getParameter('node')) {
      if (!is_object($node)) {
        $node = $this->entityTypeManager->getStorage('node')->load($node);
      }
      // If there is node add its cachetag.
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    }
    else {
      // Return default tags instead.
      return parent::getCacheTags();
    }
  }

  /**
   * Define cache contexts for custom block.
   */
  public function getCacheContexts() {
    // If you depend on \Drupal::routeMatch()
    // you must set context of this block with 'route' context tag.
    // Every new route this block will rebuild.
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
