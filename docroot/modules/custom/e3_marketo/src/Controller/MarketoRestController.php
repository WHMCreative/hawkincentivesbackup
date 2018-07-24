<?php

namespace Drupal\e3_marketo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\e3_marketo\MarketoRestHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class MarketoRestController.
 *
 * Returns responses for Marketo Rest related routes.
 *
 * @package Drupal\e3_marketo\Controller
 */
class MarketoRestController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Current API configuration.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $apiSettings;

  /**
   * Current Marketo api token status.
   *
   * @var array
   */
  protected $tokenStatus;

  /**
   * HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Current State.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Marketo REST helper.
   *
   * @var \Drupal\e3_marketo\MarketoRestHelper
   */
  protected $restHelper;

  /**
   * MarketoRestController constructor.
   *
   * @param \GuzzleHttp\Client $client
   *   HTTP client.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   Config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   State storage.
   * @param \Drupal\e3_marketo\MarketoRestHelper $marketo_rest_helper
   *   Rest helper service.
   */
  public function __construct(\GuzzleHttp\Client $client,  ConfigFactory $config_factory, StateInterface $state, MarketoRestHelper $marketo_rest_helper) {

    $this->httpClient = $client;
    $this->apiSettings = $config_factory->get('e3_marketo.settings');
    $this->state = $state;
    $this->tokenStatus = $state->get('e3_marketo.token_status');
    $this->restHelper = $marketo_rest_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('config.factory'),
      $container->get('state'),
      $container->get('e3_marketo.rest_helper')
    );
  }

  /**
   * Retrieve a set of values to prefill Marketo form with.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JsonResponse, containing field-value pairs of pre-fill data.
   */
  public function getFormValues(Request $request) {

    // Prevent this function from being exploited and called outside of ajax
    // context.
    if (!$request->isXmlHttpRequest()) {
      throw new AccessDeniedHttpException();
    }

    // Get provided parameters, api access token, rest endpoint.
    $params = $request->request->all();
    $form_fields = !empty($params['formFields']) ? $params['formFields'] : [];
    $tracking_code = !empty($params['trkValue']) ? $params['trkValue'] : '';

    // Unset Marketo and Drupal fields.
    unset($form_fields['munchkinId']);
    unset($form_fields['formid']);

    $ret_val = [];

    $data = $this->restHelper->getLeadFields($tracking_code, array_keys($form_fields));

    if ($data && !empty($data->success)) {

      // Request has been successful. Extract prefill data.
      $result = !empty($data->result) ? reset($data->result) : [];

      if (!empty($result)) {

        // Variable $result should contain returned Marketo field values.
        foreach ($form_fields as $field_name => $field_value) {

          // Marketo seems to return slightly different field names then it
          // initially accepts. For example, it accept the field "FirstName",
          // but will change it to "firstName" in the response data.
          $original_field_name = $field_name;

          if (strpos($original_field_name, '__c') === FALSE) {
            $field_name = lcfirst($field_name);
          }

          if (!empty($result->{$field_name})) {
            $ret_val[$original_field_name] = $result->{$field_name};
          }
        }
      }
    }

    return new JsonResponse($ret_val);
  }

}
