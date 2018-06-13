BHK Custom Components
================================================================================

BHK Custom Components module allows to create summary paragraph components, displaying a set amount of content items, following any preset rules.

Getting Started
--------------------------------------------------------------------------------

Summary functionality can be injected into any paragraph type. Once enabled, all logic is delegated to the selected summary handler plugin.

To create a new summary component type follow these steps:
  1. Create a new paragraph bundle. During the creation click "Enable this bundle to leverage Summary Handler functionality". Summary settings should become visible.
  2. Select a desired handler to use. Default handler assumes that there are no specific entity references to check, other handlers check for extra fields (e.g. blog handler looks for blog category and tags).
  3. Select content types that should be shown in this summary.
  4. Select desired view mode to use for the nodes and check the "Add default fields" box.
  5. Click "Save". All default fields, like Summary Title, Total Count, Summary content (for direct selection of items) are going to be automatically added to this paragraph type.    Summary manager also automatically configures these fields and their Form and View displays, so if you don't require extra conditions you don't really have to do anything manually.

Creation of a new summary handler plugin.
--------------------------------------------------------------------------------

Default Summary Handler plugin does not check any extra fields, like taxonomy references. It also sorts items by created date in descending order.

If your summary should check for additional conditions or expose new options for content editor you should add in a new summary handler plugin for this purpose and set that plugin for desired paragraph bundle.

 - System checks for available summary handler plugins in src/Plugin/SummaryHandler directory within your module. Once this module is enabled you can add you summary handlers in other custom modules as long as they are following the namespace structure.
 - Create a new file for the summary under the above-mentioned path. Summary Handlers use annotation based discovery, so in order for your class to be recognized as a new summary handler plugin it has to provide the correct annotation.
 - Annotation structure:

  /**
   * Short description of the plugin.
   *
   * @SummaryHandler(
   *   id = "your_summary_handler_id",
   *   label = @Translation("Plugin Label."),
   *   conditionFields = {
   *     "reference_field_on_paragraph" = "reference_field_on_node",
   *   }
   * )
   */

  - conditionFields element of the annotation allows you to set up the plugin to check for custom entity reference fields. Just specify the entity reference field on your paragraph bundle and corresponding node field to look for and plugin will automatically add necessary conditions.
  - For more information, look into the base summary handler class, annotation class and existing summary handlers!