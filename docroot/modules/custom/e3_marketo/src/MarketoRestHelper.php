<?php

namespace Drupal\e3_marketo;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides helper functions to access Marketo Rest API.
 *
 * @package Drupal\e3_marketo
 */
class MarketoRestHelper {

  use StringTranslationTrait;

  /**
   * HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * State storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Marketo API settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $apiSettings;

  /**
   * Logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * CventGdprHandler constructor.
   *
   * @param \GuzzleHttp\Client $client
   *   HTTP Client.
   * @param \Drupal\Core\State\StateInterface $state
   *   State Storage.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Configuration factory service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger channel factory.
   */
  public function __construct(
    Client $client,
    StateInterface $state,
    ConfigFactoryInterface $config_factory,
    LoggerChannelFactoryInterface $logger_factory) {

    $this->httpClient = $client;
    $this->state = $state;
    $this->apiSettings = $config_factory->get('e3_marketo.settings');
    $this->logger = $logger_factory->get('e3_marketo');
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
      $this->logger->error($e->getMessage());
    }

    return FALSE;
  }

  /**
   * Retrieve endpoint path for Leads requests.
   *
   * @return null|string
   *   Endpoint path or null if not set in Marketo Settings.
   */
  public function getLeadsEndpoint() {
    $endpoint = $this->apiSettings->get('endpoint_path');

    if ($endpoint) {
      $endpoint = $endpoint . '/v1/leads.json';
    }

    return $endpoint;
  }

  /**
   * Retrieve lead data for specified fields from Marketo.
   *
   * @param $tracking_cookie
   *   Marketo tracking cookie value.
   * @param $fields
   *   Array of API field names to get the data from.
   *
   * @return bool|mixed|string
   *   Retrieved Marketo Lead data.
   */
  public function getLeadFields($tracking_cookie, $fields) {
    $access_token = $this->getAccessToken();
    $endpoint = $this->getLeadsEndpoint();

    if (!$endpoint || !$access_token) {
      return FALSE;
    }

    $arguments = [
      'filterType' => 'cookie'
    ];

    // Add fields, value of the cookie and access token to query arguments.
    if (!empty($tracking_cookie) && !empty($fields)) {
      $arguments['filterValues'] = $tracking_cookie;
      $arguments['fields'] = implode(',', $fields);
      $arguments['access_token'] = $access_token;
    }
    try {
      $marketo_response = $this->httpClient->request('GET', $endpoint, ['query' => $arguments]);
      $data = $marketo_response->getBody()->getContents();
    }
    catch (\Exception $e) {
      $data = '{}';
    }

    $data = json_decode($data);

    return $data;
  }

  /**
   * Update or create Leads in Marketo.
   *
   * @param array $lead_data
   *   Array of Lead data as expected by Marketo.
   *
   * @return mixed
   *   Request result.
   *
   * @see http://developers.marketo.com/rest-api/endpoint-reference/lead-database-endpoint-reference/#!/Leads/syncLeadUsingPOST.
   */
  public function syncLeads(array $lead_data) {
    $access_token = $this->getAccessToken();
    $endpoint = $this->getLeadsEndpoint();

    // No need to proceed if any of these are missing.
    if (!$endpoint || !$access_token || !$lead_data) {
      return FALSE;
    }

    $arguments = [
      'access_token' => $access_token,
    ];

    // Create endpoint URL with access token included.
    $endpoint = Url::fromUri($endpoint, ['query' => $arguments]);

    // Send post request to sync the lead.
    try {
      $marketo_response = $this->httpClient->post($endpoint->toString(), [
        'body' => \GuzzleHttp\json_encode($lead_data),
        'headers' => [
          'Accept' => "application/json",
          'Content-Type' => "application/json",
        ],
        'http_errors' => FALSE,
      ]);

      $data = $marketo_response->getBody()->getContents();
    }
    catch (\Exception $e) {
      $data = '{}';
    }

    return $data;
  }

}
