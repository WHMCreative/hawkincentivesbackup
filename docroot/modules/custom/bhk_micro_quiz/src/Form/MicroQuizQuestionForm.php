<?php

namespace Drupal\bhk_micro_quiz\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MicroQuizQuestionForm.
 *
 * @ingroup bhk_micro_quiz
 */
class MicroQuizQuestionForm extends FormBase {

  protected static $questions;

  /**
   * Renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'micro_quiz_question';
  }

  /**
   * Constructs a \Drupal\e3_marketo\Form\MarketoFormEntitySettingsForm object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer service.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer')
    );
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }

  /**
   * Defines the question form for MicroQuiz components.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   * @throws \Exception
   */
  public function buildForm(array $form, FormStateInterface $form_state, array $variables = []) {

    // Sanity check. This form is only valid when built from paragraph preprocessing.
    if (empty($variables['paragraph'])) {
      $form['#markup'] = $this->t('Error. Attempting to build a MicroQuiz without questions.');
      return $form;
    }

    // Determine the total number of questions and the current step.
    /** @var \Drupal\Core\Entity\FieldableEntityInterface $parent */
    $parent = $variables['paragraph']->getParentEntity();
    if (!isset(static::$questions[$parent->id()])) {

      /** @var \Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList $questions */
      $questions = $parent->get('field_p_questions');
      static::$questions[$parent->id()]['total'] = $questions->count();
      static::$questions[$parent->id()]['current'] = 1;

      $form['step'] = array(
        '#type' => 'value',
        '#value' => 'active',
      );
    }
    else {
      static::$questions[$parent->id()]['current']++;

      if (static::$questions[$parent->id()]['current'] === static::$questions[$parent->id()]['total']) {
        $last_question = TRUE;
      }

      $form['step'] = array(
        '#type' => 'value',
        '#value' => 'hidden',
      );
    }

    $index = Element::children($variables['content']['field_question']);
    $index = reset($index);

    $question = $variables['content']['field_question'][$index];
    $answers = $variables['content']['field_answers'];

    $options = [];
    foreach (Element::children($answers) as $key) {
      $options[$key] = $this->renderer->render($answers[$key]);
    }

    $form['#attached']['library'] = [
      'bhk_micro_quiz/bhk_micro_quiz.commands',
    ];

    $form['microquiz_question'] = [
      '#type' => 'radios',
      '#title' => $this->renderer->render($question),
      '#options' => $options,
    ];

    $question_counter = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['current-question'],
      ],
      '#markup' => $this->t('@current of @total', [
        '@current' => static::$questions[$parent->id()]['current'],
        '@total' => static::$questions[$parent->id()]['total'],
      ]),
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'next' => [
        '#type' => 'submit',
        '#value' => empty($last_question) ? $this->t('Next') : $this->t('Get Result'),
        '#attributes' => [
          'class' => [
            empty($last_question) ? 'quiz-action-next' : 'quiz-action-finish',
          ],
          'disabled' => 'disabled',
        ],
        '#suffix' => $this->renderer->render($question_counter),
      ],
    ];

    return $form;
  }

}
