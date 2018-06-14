<?php

namespace Drupal\e3_marketo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Config\ConfigFactory;

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
   * {@inheritdoc}
   */
  public function __construct(\GuzzleHttp\Client $client,  ConfigFactory $config_factory, StateInterface $state) {

    $this->httpClient = $client;
    $this->apiSettings = $config_factory->get('e3_marketo.settings');

    $this->state = $state;
    $this->tokenStatus = $state->get('e3_marketo.token_status');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('config.factory'),
      $container->get('state')
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

    // Get provided parameters, api access token, rest endpoint.
    $params = $request->request->all();
    $access_token = $this->getAccessToken();
    $endpoint = $this->apiSettings->get('endpoint_path');
    $endpoint = $endpoint . '/v1/leads.json';

    $form_fields = !empty($params['formFields']) ? $params['formFields'] : [];
    $tracking_code = !empty($params['trkValue']) ? $params['trkValue'] : '';

    $arguments = [
     'filterType' => 'cookie'
    ];

    // Unset Marketo and Drupal fields.
    unset($form_fields['munchkinId']);
    unset($form_fields['formid']);

    $ret_val = [];

    // Add fields, value of the cookie and access token to query arguments.
    if (!empty($tracking_code) && !empty($form_fields)) {
     $arguments['filterValues'] = $tracking_code;
     $arguments['fields'] = implode(',', array_keys($form_fields));
     $arguments['access_token'] = $access_token;
    }
    try {
      $marketo_request = $this->httpClient->request('GET', $endpoint, ['query' => $arguments]);
      $data = $marketo_request->getBody()->getContents();
    }
    catch (\Exception $e) {
      $data = '{}';
    }

    $data = json_decode($data);

    if (!empty($data->success)) {

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

  /**
   * Get access token either from storage or identity endpoint.
   *
   * @return string|boolean
   *   Either retrieved token, or FALSE if operation failed.
   */
  public function getAccessToken() {
    // Retrieve stored token information, if available.
    $token_data = $this->state->get('e3_marketo.token_status');

    // Check if token has expired or is close to expiration (1 min).
    if ($token_data && ($token_data['expires_in'] - time() > 60)) {
      return $token_data['access_token'];
    }
    else {
      // Otherwise attempt to generate a new token.
      $token_data = $this->generateAccessToken();

      if ($token_data) {
        return $token_data['access_token'];
      }
    }

    return FALSE;
  }

  /**
   * Generate access token, using provided identity endpoint.
   * 
   * @return array|boolean
   *   Retrieved token data, or FALSE if operation failed.
   */
  public function generateAccessToken() {
    // Retrieve ClientID, secret, build identity endpoint.
    $client_id = $this->apiSettings->get('client_id');
    $client_secret = $this->apiSettings->get('client_secret');
    $identity_endpoint = "{$this->apiSettings->get('identity_endpoint_path')}/oauth/token";

    // Attempt to retrieve an access token with given settings.
    try {
      $response = $this->httpClient->get("{$identity_endpoint}?grant_type=client_credentials&client_id={$client_id}&client_secret={$client_secret}");

      $result = \GuzzleHttp\json_decode($response->getBody()->getContents());

      // If successful, save retrieved token data.
      if (!empty($result->access_token)) {
        $token_data = [
          'access_token' => $result->access_token,
          'expires_in' => time() + $result->expires_in
        ];

        $this->state->set('e3_marketo.token_status', $token_data);
        return $token_data;
      }

    }
    catch (RequestException $e) {
      watchdog_exception('e3_marketo', $e, $e->getMessage());
    }

    return FALSE;
  }

}
