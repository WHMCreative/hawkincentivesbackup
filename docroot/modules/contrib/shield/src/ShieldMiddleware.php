<?php

namespace Drupal\shield;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Path\PathMatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Middleware for the shield module.
 */
class ShieldMiddleware implements HttpKernelInterface {

  /**
   * Constants representing if configured paths should be included
   * or excluded from Shield protection.
   */
  const EXCLUDE_METHOD = 0;
  const INCLUDE_METHOD = 1;

  /**
   * The decorated kernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Constructs a BanMiddleware object.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The decorated kernel.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(HttpKernelInterface $http_kernel, ConfigFactoryInterface $config_factory, PathMatcherInterface $path_matcher) {
    $this->httpKernel = $http_kernel;
    $this->configFactory = $config_factory;
    $this->pathMatcher = $path_matcher;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    $config = $this->configFactory->get('shield.settings');
    $allow_cli = $config->get('allow_cli');

    switch ($config->get('credential_provider')) {
      case 'shield':
        $user = $config->get('credentials.shield.user');
        $pass = $config->get('credentials.shield.pass');
        break;
      case 'key':
        $user = $config->get('credentials.key.user');

        /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
        $storage = \Drupal::entityTypeManager()->getStorage('key');
        /** @var \Drupal\key\KeyInterface $pass_key */
        $pass_key = $storage->load($config->get('credentials.key.pass_key'));
        if ($pass_key) {
          $pass = $pass_key->getKeyValue();
        }
        break;
      case 'multikey':
        /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
        $storage = \Drupal::entityTypeManager()->getStorage('key');
        /** @var \Drupal\key\KeyInterface $user_pass_key */
        $user_pass_key = $storage->load($config->get('credentials.multikey.user_pass_key'));
        if ($user_pass_key) {
          $values = $user_pass_key->getKeyValues();
          $user = $values['username'];
          $pass = $values['password'];
        }
        break;
    }

    $bypass = $auth = FALSE;

    // Should the current path be protected?
    $allow_path = $this->checkPathAllowed($request, $config);

    if ($allow_path || $type != self::MASTER_REQUEST || !$user || (PHP_SAPI === 'cli' && $allow_cli)) {
      // Bypass:
      // 1. Subrequests
      // 2. Empty username config (meaning module is effectively disabled).
      // 3. CLI requests if CLI is allowed.
      $bypass = TRUE;
    }
    else {
      if ($request->server->has('PHP_AUTH_USER') && $request->server->has('PHP_AUTH_PW')) {
        $input_user = $request->server->get('PHP_AUTH_USER');
        $input_pass = $request->server->get('PHP_AUTH_PW');
      }
      elseif (!empty($request->server->get('HTTP_AUTHORIZATION'))) {
        list($input_user, $input_pass) = explode(':', base64_decode(substr($request->server->get('HTTP_AUTHORIZATION'), 6)), 2);
      }
      elseif (!empty($request->server->get('REDIRECT_HTTP_AUTHORIZATION'))) {
        list($input_user, $input_pass) = explode(':', base64_decode(substr($request->server->get('REDIRECT_HTTP_AUTHORIZATION'), 6)), 2);
      }

      if (isset($input_user) && $input_user === $user && Crypt::hashEquals($pass, $input_pass)) {
        $auth = TRUE;
      }
    }

    if ($bypass || $auth) {
      return $this->httpKernel->handle($request, $type, $catch);
    }

    $response = new Response();
    $response->headers->add([
      'WWW-Authenticate' => 'Basic realm="' . strtr($config->get('print'), [
          '[user]' => $user,
          '[pass]' => $pass,
        ]) . '"',
    ]);
    $response->setStatusCode(401);
    return $response;
  }

  /**
   * Checks if the current path should be allowed to bypass shield.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The global request object.
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   The current Shield config.
   *
   * @return bool
   *   TRUE if the current path should be bypassed, and FALSE if not.
   */
  public function checkPathAllowed(Request $request, ImmutableConfig $config) {
    $path_match = $this->pathMatcher->matchPath(\Drupal::service('path.alias_manager')->getAliasByPath($request->getPathInfo()), $config->get('paths'));
    $method = $config->get('method');
    return $path_match && $method == self::EXCLUDE_METHOD || !$path_match && $method == self::INCLUDE_METHOD;
  }

}
