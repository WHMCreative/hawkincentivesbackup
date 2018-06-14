<?php

namespace Drupal\editor_advanced_link\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\ckeditor\CKEditorPluginContextualInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "linkitstyles" plugin.
 *
 * NOTE: The plugin ID ('id' key) corresponds to the CKEditor plugin name.
 * It is the first argument of the CKEDITOR.plugins.add() function in the
 * plugin.js file.
 *
 * @CKEditorPlugin(
 *   id = "linkitstyles",
 *   label = @Translation("LinkIt Styles")
 * )
 */
class LinkitStyles extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface, CKEditorPluginContextualInterface{


  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return TRUE;
  }

  /**
   * @param \Drupal\editor\Entity\Editor $editor
   *
   * @return bool
   */
  public function isEnabled(Editor $editor) {
    // Get editor settings and enable accordingly
    $settings = $editor->getSettings();
    return isset($settings['plugins']['linkitstyles']) ? $settings['plugins']['linkitstyles']['enable'] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param \Drupal\editor\Entity\Editor $editor
   *
   * @return array
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    // Defaults.
    $config = [
      'enable' => '',
      'styles' => '',
    ];
    $settings = $editor->getSettings();

    // Get enabled value if available
    if ( isset($settings['plugins']['linkitstyles']) && isset($settings['plugins']['linkitstyles']['enable']) ) {
      $config['enable'] = $settings['plugins']['linkitstyles']['enable'];
    }

    // Get Style if available
    if ( isset($settings['plugins']['linkitstyles']) && isset($settings['plugins']['linkitstyles']['styles']) ) {
      $config['styles'] = $settings['plugins']['linkitstyles']['styles'];
    }

    // Add enabled checkbox
    $form['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Give a predefined set of classes for LinkIt Styles'),
      '#description' => $this->t('If checked, a select list will be available on the LinkIt dialog box to select the below options'),
      '#default_value' => !empty($config['enable']) ? $config['enable'] : FALSE,
    ];

    // Add text area for options
    $form['styles'] = [
      '#title' => $this->t('Styles'),
      '#title_display' => 'invisible',
      '#type' => 'textarea',
      '#default_value' => $config['styles'],
      '#description' => $this->t('A list of classes that will be provided in the LinkIt dialog "Styles" dropdown. Enter one or more classes on each line in the format: .classA.classB|Label. Example: .primary|Primary. Advanced example: .primary.btn|Primary Button.<br />These styles should be available in your theme\'s CSS file.'),
      '#element_validate' => [
        [$this, 'validateStylesValue'],
      ],
    ];
    return $form;
  }

  /**
   * #element_validate handler for the "styles" element in settingsForm().
   */
  public function validateStylesValue(array $element, FormStateInterface $form_state) {
    $styles_setting = $this->generateStylesSetSetting($element['#value']);
    if ($styles_setting === FALSE) {
      $form_state->setError($element, $this->t('The provided list of linkit styles is syntactically incorrect.'));
    }
    else {
      $style_names = array_map(function ($style) {
        return $style['name'];
      }, $styles_setting);
      if (count($style_names) !== count(array_unique($style_names))) {
        $form_state->setError($element, $this->t('Each linkit style must have a unique label.'));
      }
    }
  }

  /**
   * Builds the "linkitStylesSet" configuration part of the CKEditor JS settings.
   *
   * @see getConfig()
   *
   * @param string $styles
   *   The "styles" setting.
   * @return array|false
   *   An array containing the "linkitStylesSet" configuration, or FALSE when the
   *   syntax is invalid.
   */
  protected function generateStylesSetSetting($styles) {
    $styles_set = [];

    // Early-return when empty.
    $styles = trim($styles);
    if (empty($styles)) {
      return $styles_set;
    }

    $styles = str_replace(["\r\n", "\r"], "\n", $styles);
    foreach (explode("\n", $styles) as $style) {
      $style = trim($style);

      // Ignore empty lines in between non-empty lines.
      if (empty($style)) {
        continue;
      }

      // Validate syntax: [.class...]|label pattern expected.
      // @TODO Is this the best regex?
      if (!preg_match('@^(\\. *[a-zA-Z0-9\-]+) *(\\. *[a-zA-Z0-9\-]+ *)*\\| *.+ *$@', $style)) {
        return FALSE;
      }

      // Parse.
      list($selector, $label) = explode('|', $style);
      $classes = explode('.', $selector);

      // Build the data structure CKEditor's stylescombo plugin expects.
      // @see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Styles
      $configured_style = [
        'name' => trim($label),
      ];
      if (!empty($classes)) {
        $configured_style['attributes'] = [
          'class' => implode(' ', array_map('trim', $classes))
        ];
      }
      $styles_set[] = $configured_style;
    }
    return $styles_set;
    }
}
