INTRODUCTION
------------

 E3 Marketo module provides the tools for the base integration with Marketo Forms.
 
 The main intent for this module is for it to be used as a basis with a 
 supporting project-specific modules providing extensions for available 
 functionality, depending on the project needs.
 
MAIN FEATURES
-------------

 * Marketo Form entity with bundle support.
 * Injection of Marketo forms via Marketo Forms 2.0.
 * Marketo Forms prefill.
 * Clean up of all Marketo-sourced styles for easier project-specific theming.
 * Ability to set hidden fields when referencing the form.
 * Built in admins views to manage the forms collection.
 
CONFIGURATION
-------------

 In order to be able to inject Marketo Forms, visit the settings page at 
 admin/config/marketo or from Configuration -> Web Services -> Marketo Settings.
 
 Marketo instance host and Munchkin ID are the only required parameters to embed
 Marketo Forms. However, in order for pre-fill to function, all other parameters
 have to be set as well. This includes:
 
 * Client ID
 * Client Secret
 * Rest Endpoint Path
 * Identity Endpoint Path
 
 See http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api 
 to make sure these are available and set up within the Marketo instance.
 
USAGE
-----

 New Marketo Forms can be added through marketo_form/add or via 
 Content -> Marketo Forms -> Add a new Marketo Form
 
 Marketo Bundles are available under admin/structure/marketo_bundles.
 
 Existing list of Marketo Forms can be viewed at admin/content/marketo-forms.
 
 Module provides the "Reference - Marketo Form" component out of the box. It can 
 be used to add Marketo Forms to pages as well as to set Hidden Values for 
 Marketo Forms.
 
 Marketo Forms can also utilize the "Marketo - Submission Confirmation"
 component. If added to the form, this component will replace the form once it 
 has been successfully submitted to Marketo. If this component is not added,
 default functionality set up from the side of Marketo will be used instead.
 
EXTENDING THE MODULE
--------------------

 Anything related to Marketo Forms embed and submission logic can be overridden
 via the custom Marketo Handler plugins.
 
 Marketo handler plugins are responsible for embedding the form, providing 
 attributes and settings to use withing javascript as well as the list of 
 javascript functions to execute after the form has been successfully submitted.
 
 Marketo Handler file must be placed within the src/Plugin/MarketoHandler 
 directory of your module in order for system to discover it.
 
 Example of the handler:
 
 ```php
 /**
   * Example Handler.
   *
   * @MarketoHandler(
   *   id = "example_marketo_handler",
   *   label = @Translation("Example Marketo Handler"),
   *   description = @Translation("Example description."),
   *   bundles = {
   *     "default",
   *   },
   *   priority = 100
   * )
   */
  class ExampleMarketoHandler extends DefaultMarketoHandler {
  }
 ```
 Annotation parameters:
 * id - unique ID of the plugin.
 * label - self-explanatory.
 * description - self-explanatory.
 * bundles - list of Marketo Form bundles the plugin applies to. Handler will 
   only affect these bundles. Leave empty to apply the handler to all bundles.
 * priority - priority of execution. If there are several handlers that are 
   going to affect a Marketo Form, higher priority handlers will have their 
   callbacks executed before the lower priority ones. This allows to specify the
   exact order for the handlers to be executed in.
 
 See DefaultMarketoHandler and MarketohandlerInterface for the complete list of
 methods that can be overriden within the custom handler. 
 
 Most important methods:
 * applies() - this function allows to define a custom logic that determines 
   whether the plugin can be applied in the current context.
 * embedMarketoForm() - this is the main function that determines whet goes into
   the 'marketo_form_embed' extra field.
 * alterScriptParameters() - this function allows to alter the list of script
   parameters that will be sent to the javascript.
 * getSubmissionCallbacks() - this function determines the list of JS function 
   that will be executed (if they exist) upon the form submission. It can be 
   overriden in a custom handler to remove any functions set by other handlers 
   from the list or to add your own. Added functions MUST be a part of 
   Drupal.behaviors.marketoForm.
   
POSSIBLE ISSUES AND SOLUTIONS
-----------------------------

 1. Pre-fill doesn't work even though all Marketo settings are correcntly filled
 in.
    - Turn off any ad-blocking browser extensions.
 2. Hidden fields are not populated when set through "Reference - Marketo Form"
 component.
    - Hidden fields must be added to the form at the side of Marketo before they
    can set and captured within the submission.
 3. How to completely remove/replace the default Marketo Submission logic?
    - Override getSubmissionCallbacks() function within a custom handler and
    remove/replace the default callback specified there.
    - Override Drupal.behaviors.marketoForms.submitMarketoForm with a different
    function in your own javascript. Add the base script as a dependency.
 4. How to extend the base javascript that embeds the form?
    - Add your own js in the custom module, then add a dependency on the main js
    file.
 Example:
 ```yaml
  my-custom-marketo-script:
    version: 1.x
    js:
      js/my-custom-marketo-script.js: {}
    dependencies:
      - e3_marketo/marketo-forms
 ```
 5. How to access the marketo settings in the custom script?
    - All marketo script settings are available under the `settings.marketoForms`
    parameter in your Drupal Behavior.