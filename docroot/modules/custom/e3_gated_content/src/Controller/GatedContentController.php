<?php

namespace Drupal\e3_gated_content\Controller;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Controller for processing the gated content unlocks.
 */
class GatedContentController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheInvalidator;

  /**
   * GatedContentController constructor.
   *
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cache_tags_invalidator
   *   Cache invalidator.
   */
  public function __construct(CacheTagsInvalidatorInterface $cache_tags_invalidator) {
    $this->cacheInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * Process the gated resource unlock request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   *
   * @return \Drupal\Core\Routing\LocalRedirectResponse|\Symfony\Component\HttpFoundation\JsonResponse
   *   Either Json response or redirect if successfully unlocked.
   */
  public function unlockGatedResource(Request $request) {

    // Return if error occurred.
    if (!$request->request->count()) {
      return new JsonResponse(FALSE);
    }

    $params = $request->request->all();
    $unlocked_content = isset($params['unlocked_content']) ? $params['unlocked_content'] : NULL;

    if ($unlocked_content) {
      $cookies = $request->cookies;

      // Look for existing cookie first, update it if found.
      $unlocked_gated_resources = $cookies->get('bhk_unlocked_resources', []);
      if ($unlocked_gated_resources) {
        $unlocked_gated_resources = json_decode($unlocked_gated_resources);

        if (!in_array($unlocked_content, $unlocked_gated_resources)) {
          $unlocked_gated_resources[] = $unlocked_content;
        }
      }
      else {
        $unlocked_gated_resources[] = $unlocked_content;
      }

      // Retrieve the extra query parameters to add after unlocking.
      $params = $this->getQueryParameters($unlocked_content);

      $query_string = '';
      foreach ($params as $param_key => $param_value) {

        if (empty($query_string)) {
          $query_string = "{$param_key}={$param_value}";
        }
        else {
          $query_string .= "&{$param_key}={$param_value}";
        }
      }

      $response = new JsonResponse(['query_string' => $query_string]);
      $response->headers->setCookie(new Cookie('bhk_unlocked_resources', json_encode($unlocked_gated_resources), 0, '/', $request->getHost(), FALSE, FALSE));
      $response->headers->setCookie(new Cookie('gated_unlock_first_view', 1, strtotime('+1 hour'), '/', $request->getHost(), FALSE, FALSE));

      // Force cache invalidation for the source entity.
      $this->cacheInvalidator->invalidateTags(["node:{$unlocked_content}"]);

      return $response;
    }

    return new JsonResponse(FALSE);
  }

  /**
   * Retrieve the query parameters to add to the page after the reload.
   *
   * @param $nid
   *  Node ID.
   *
   * @return array|bool
   */
  public function getQueryParameters($nid) {
    $node = Node::load($nid);

    if ($node && $node->hasField('field_p_form') && !$node->get('field_p_form')->isEmpty()) {
      /** @var \Drupal\paragraphs\ParagraphInterface $gated_form */
      $gated_form = $node->get('field_p_form')->first()->entity;

      if (!$gated_form) {
        return FALSE;
      }

      $params = [
        'state' => 'unlocked',
      ];

      return $params;
    }

    return FALSE;
  }

}
