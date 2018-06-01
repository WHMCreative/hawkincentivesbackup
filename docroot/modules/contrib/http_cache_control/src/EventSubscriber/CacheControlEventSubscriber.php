<?php
namespace Drupal\http_cache_control\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Subscriber for adding http cache ontrol headers.
 */
class CacheControlEventSubscriber implements EventSubscriberInterface {

  /**
   * Set http cache control headers.
   */
  public function setHeaderCacheControl(FilterResponseEvent $event) {
    $config = \Drupal::service('config.factory')->get('system.performance');

    $response = $event->getResponse();

    if (!$response->isCacheable()) {
      return;
    }

    $max_age = $response->getMaxAge();

    switch ($response->getStatusCode()) {
      case 404:
        $ttl = $config->get('cache.http.404_max_age', $max_age);
        break;

      case 302:
        $ttl = $config->get('cache.http.302_max_age', $max_age);
        break;

      default:
        $ttl = $config->get('cache.page.max_age');
        break;
    }

    // Allow modules that set thier own max age to retain it.
    if ($max_age != $config->get('cache.page.max_age')) {
      $ttl = $max_age;
    }
    $response->setSharedMaxAge($ttl);
    $response->setClientTtl($config->get('cache.http.max_age'));

    if ($response->getStatusCode() >= 500) {
      $response->setSharedMaxAge($config->get('cache.http.5xx_max_age'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Response: set header content for security policy.
    $events[KernelEvents::RESPONSE][] = ['setHeaderCacheControl', -10];
    return $events;
  }

}
