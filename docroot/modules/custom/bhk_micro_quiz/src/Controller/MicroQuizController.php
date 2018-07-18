<?php

namespace Drupal\bhk_micro_quiz\Controller;

use Drupal\bhk_micro_quiz\Ajax\ReloadPageAjaxCommand;
use Drupal\bhk_micro_quiz\Service\MicroQuizHelper;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class MicroQuizController.
 *
 * Returns responses for MicroQuiz-related routes.
 *
 * @package Drupal\bhk_micro_quiz\Controller
 */
class MicroQuizController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * MicroQuiz helper service.
   *
   * @var \Drupal\bhk_micro_quiz\Service\MicroQuizHelper
   */
  protected $quizHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(MicroQuizHelper $quiz_helper) {
    $this->quizHelper = $quiz_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bhk_micro_quiz.quiz_helper')
    );
  }

  /**
   * Retrieve survey result.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JsonResponse, containing the survey result or no results message.
   */
  public function getSurveyResults(Request $request) {

    // Prevent this function from being exploited and called outside of ajax
    // context.
    if (!$request->isXmlHttpRequest()) {
      throw new AccessDeniedHttpException();
    }

    // Get provided parameters, api access token, rest endpoint.
    $params = $request->request->all();

    // Get Survey Results.
    try {
      $output = $this->quizHelper->getSurveyResult($params['answers']);
    } catch (InvalidPluginDefinitionException $e) {
      return new JsonResponse(FALSE);
    } catch (\Exception $e) {
      return new JsonResponse(FALSE);
    }

    // Update Marketo lead.
    $this->quizHelper->updateMarketoLead($request);

    return new JsonResponse(['result' => $output]);
  }

  /**
   * Reset MicroQuiz.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current Request.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   AjaxResponse to reload the page.
   */
  public function resetMicroQuiz(Request $request) {

    // Prevent this function from being exploited and called outside of ajax
    // context.
    if (!$request->isXmlHttpRequest()) {
      throw new AccessDeniedHttpException();
    }

    // Reload the page and remove all MicroQuiz cookies.
    $response = new AjaxResponse();
    $response->headers->clearCookie('mq_answers', '/', $request->getHost());
    $response->headers->clearCookie('mq_intention', '/', $request->getHost());
    $response->headers->clearCookie('mq_quantity', '/', $request->getHost());
    $response->addCommand(new ReloadPageAjaxCommand());

    return $response;
  }

}
