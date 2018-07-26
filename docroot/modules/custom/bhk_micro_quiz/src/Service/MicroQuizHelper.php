<?php

namespace Drupal\bhk_micro_quiz\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\e3_marketo\MarketoRestHelper;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides MicroQuiz helper functions.
 *
 * @package Drupal\bhk_micro_quiz
 */
class MicroQuizHelper {

  use StringTranslationTrait;

  /**
   * HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Marketo Rest helper service.
   *
   * @var \Drupal\e3_marketo\MarketoRestHelper
   */
  protected $restHelper;

  /**
   * Message to display when Survey has no valid results.
   *
   * @var string
   */
  protected $noResultsMessage = 'No results found';

  /**
   * Text of the cookie reset button.
   *
   * @var string
   */
  protected $restartButtonText = 'Start Over';

  /**
   * CventGdprHandler constructor.
   *
   * @param \GuzzleHttp\Client $client
   *   HTTP Client.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type Manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer service.
   * @param \Drupal\e3_marketo\MarketoRestHelper $rest_helper
   *   Marketo Rest helper service.
   */
  public function __construct(
    Client $client,
    EntityTypeManagerInterface $entity_type_manager,
    RendererInterface $renderer,
    MarketoRestHelper $rest_helper) {

    $this->httpClient = $client;
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->restHelper = $rest_helper;
  }

  /**
   * Build Survey result.
   *
   * @param $answers
   *   Answers array.
   *
   * @return \Drupal\Component\Render\MarkupInterface
   *   Markup for the Survey result.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function getSurveyResult($answers) {
    $paragraphManager = $this->entityTypeManager->getStorage('paragraph');

    // Build base query to work with.
    $query = $paragraphManager->getQuery()
      ->condition('type', 'microquiz_conditional');

    // Add answer-based conditions.
    foreach ($answers as $key => $answer_id) {

      // Current limit is 2 answers per quiz. Make sure this is not exceeded.
      if ($key > 1) {
        break;
      }

      $field_key = $key + 1;
      $query->condition("field_q{$field_key}_answer.target_id", $answer_id);
    }

    $results = $query->execute();

    // Determine and build the results render array.
    if ($results) {
      $result = reset($results);

      /** @var \Drupal\paragraphs\ParagraphInterface $result */
      $result = $paragraphManager->load($result);
      $parent = $result->getParentEntity();

      $render_array['result'] = $this->entityTypeManager->getViewBuilder('micro_quiz')->view($parent);
    }
    else {
      $render_array['result'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['no-result'],
        ],
        '#markup' => $this->noResultsMessage,
      ];
    }

    // Add a button to reset cookies.
    $render_array['reset_link'] = [
      '#type' => 'link',
      '#title' => $this->restartButtonText,
      '#url' => Url::fromRoute('bhk_micro_quiz.reset'),
      '#attached' => [
        'library' => [
          'core/drupal.ajax',
          'bhk_micro_quiz/bhk_micro_quiz.commands',
        ],
      ],
      '#options' => [
        'attributes' => [
          'class' => ['use-ajax','button', 'micro-quiz-restart'],
        ],
      ],
    ];

    // Render the results.
    $output = $this->renderer->render($render_array);

    return $output;
  }

  /**
   * Update Marketo Lead information with microquiz data.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request.
   *
   * @return bool
   *   Update result.
   */
  public function updateMarketoLead(Request $request) {

    // Grab Marketo cookie.
    $cookies = $request->cookies;
    $marketo_cookie = $cookies->get('_mkto_trk');

    // If munchkin cookie doesn't exist, API calls won't work.
    if (!$marketo_cookie) {
      return FALSE;
    }

    $data = $this->restHelper->getLeadFields($marketo_cookie, ['mqintention', 'mqquantity']);

    if ($data && !empty($data->success)) {

      // Request has been successful. Time to get results.
      $result = !empty($data->result) ? reset($data->result) : [];

      // Only update this Lead if it actually exists in Marketo.
      if (!empty($result)) {

        // Build the data for the update request to Marketo.
        $params = $request->request->all();

        /**
         * Array of lead data to update as expected by Marketo.
         *
         * @see http://developers.marketo.com/rest-api/endpoint-reference/lead-database-endpoint-reference/#!/Leads/syncLeadUsingPOST.
         */
        $lead_data = [
          'action' => 'updateOnly',
          'lookupField' => 'id',
          'input' => [
            [
              'id' => $result->id,
              'mqintention' => $params['mq_intention'],
              'mqquantity' => $params['mq_quantity'],
            ],
          ],
        ];

        // Update the lead.
        return $this->restHelper->syncLeads($lead_data);
      }
    }

    return FALSE;
  }

}
