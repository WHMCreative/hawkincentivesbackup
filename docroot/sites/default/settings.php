<?php

/**
 * Config settings
 * @TODO: Move to the repo root
 */

$config_directories = array(
    CONFIG_SYNC_DIRECTORY => '../config/default',
 );

/**
 * Hash salt used for one-time login links, etc.
 */
$settings['hash_salt'] = '_2vpc4MByU3bbDhtGSYgmxQ-lrKHWf4Tqvv5_aUSw4xy9YqpKXGvrPTWNbhGGhFnkD4T5Dhlng';

/**
 * Access control for update.php script.
 */
$settings['update_free_access'] = FALSE;

/**
 * Authorized file system operations.
 */
$settings['allow_authorize_operations'] = FALSE;

/**
 * Default mode for directories and files written by Drupal.
 */
$settings['file_chmod_directory'] = 0775;
$settings['file_chmod_file'] = 0664;

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';


$settings['install_profile'] = 'standard';

/**
 * Trusted host configuration.
 *
 * Drupal core can use the Symfony trusted host mechanism to prevent HTTP Host
 * header spoofing.
 *
 * To enable the trusted host mechanism, you enable your allowable hosts
 * in $settings['trusted_host_patterns']. This should be an array of regular
 * expression patterns, without delimiters, representing the hosts you would
 * like to allow.
 *
 * For example:
 * @code
 * $settings['trusted_host_patterns'] = array(
 *   '^www\.example\.com$',
 * );
 * @endcode
 * will allow the site to only run from www.example.com.
 *
 * If you are running multisite, or if you are running your site from
 * different domain names (eg, you don't redirect http://www.example.com to
 * http://example.com), you should specify all of the host patterns that are
 * allowed by your site.
 *
 * For example:
 * @code
 * $settings['trusted_host_patterns'] = array(
 *   '^example\.com$',
 *   '^.+\.example\.com$',
 *   '^example\.org$',
 *   '^.+\.example\.org$',
 * );
 * @endcode
 * will allow the site to run off of all variants of example.com and
 * example.org, with all subdomains included.
 */

// Trusted host patterns for e3develop and e3stanging. Make sure to add appropriate variations for production domain
// and any additional version thereof.
// Additional env specific patterns can be added in the following files (drupalvm, local)
$settings['trusted_host_patterns'] = array(
  '^e3develop\.com$',
  '^.+\.e3develop\.com$',
  '^e3staging\.com$',
  '^.+\.e3staging\.com$',
);

// Set default paths to public, private and temp directories.
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = '../private';
$config['system.file']['path']['temporary'] = '../private/tmp';

// Remove shield print message by default.
$config['shield.settings']['print'] = '';

// Allow cli to bypass shield.
$config['shield.settings']['allow_cli'] = TRUE;

// Set logging level default.
$config['system.logging']['error_level'] = 'all';

// Set Google Analytics to NULL, override this for production environment.
$config['']['account'] = '';

// Disable dev modules on all environments by default.
$config['config_split.config_split.config_dev']['status'] = FALSE;

// If $_SERVER['AH_SITE_ENVIRONMENT'], load Blackmesh settings.
if(isset($_SERVER['AH_SITE_ENVIRONMENT'])) {

  $config['system.performance']['css']['preprocess'] = TRUE;
  $config['system.performance']['js']['preprocess'] = TRUE;

  // Set GTM Code default
  $config['e3_google_tag.settings']['gtm_code'] = '';

  $config['environment_indicator.indicator']['bg_color'] = '#930007';
  $config['environment_indicator.indicator']['fg_color'] = '#ffffff';
  $config['environment_indicator.indicator']['name'] = 'BlackMesh ' . $_SERVER['AH_SITE_ENVIRONMENT'];

  // Set trusted host pattern for the acquia paragon site. We need to set this because we cannot add additional
  // aliases to a free acquia account. This can be deleted for any new project created from paragon.
  $settings['trusted_host_patterns'][] = 'bhk-d8.dev.e3develop.com';
  $settings['trusted_host_patterns'][] = 'bhk-d8.prod.e3develop.com';
  $settings['trusted_host_patterns'][] = 'hawkincentives.com';

  /**
   * Set default config_readonly status to TRUE on all Acquia environments.
   */
  //if (PHP_SAPI !== 'cli') {
  //  $settings['config_readonly'] = FALSE;
  //}

  switch ($_SERVER['AH_SITE_ENVIRONMENT']) {
    case 'dev':
      // Configure shield for dev environment.
      $config['shield.settings']['credentials']['shield']['user'] = 'hawkincentives';
      $config['shield.settings']['credentials']['shield']['pass'] = '3ditHawk';

      $settings['file_private_path'] = "/var/www/bhk-d8.dev.e3develop.com/application/shared/private";
      $config['system.file']['path']['temporary'] = "/var/www/bhk-d8.dev.e3develop.com/application/shared/private/tmp";

      /**
       * Load the development services definition file.
       */
      $settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

      /**
       * Master DB and Config Read-Only settings
       *
       * Set the environment indicator for the environment with the Master DB.  This should never be on more than one DB.
       * If non-developers are allowed to modify configuration on the master environment, add the following line:
       *
       * $settings['config_readonly'] = FALSE;
       *
       * NOTE: If set to FALSE, caution should be used when merging in config changes.
       * All Master DB config must be merged into the master branch before merging new config from VCS.
       *
       */
      $config['environment_indicator.indicator']['name'] = 'BlackMesh ' . $_SERVER['AH_SITE_ENVIRONMENT'] . ' [Master DB]';
      $config['environment_indicator.indicator']['bg_color'] = '#000000';

      break;
    case 'prod':

      //@TODO remove before launch
      $config['shield.settings']['credentials']['shield']['user'] = 'hawkincentives';
      $config['shield.settings']['credentials']['shield']['pass'] = '3ditHawk';

      //@TODO Configure this
        $settings['file_private_path'] = "/var/www/bhk-d8.prod.e3develop.com/application/shared/private";
        $config['system.file']['path']['temporary'] = "/var/www/bhk-d8.prod.e3develop.com/application/shared/private/tmp";

      // Set logging level on production.
      $config['system.logging']['error_level'] = 'hide';

      //@TODO update before launch
      // Set GTM Code
      $config['e3_google_tag.settings']['gtm_code'] = '';
      break;
  }
}
// If drupal-vm settings exist, load them.
elseif (file_exists(__DIR__ . '/settings.drupalvm.php')) {
  include __DIR__ . '/settings.drupalvm.php';
}

// If local settings file exists, load it.
if(file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}

// If local settings file exists, load it.
if(file_exists(__DIR__ . '/local-settings.inc')) {
  include __DIR__ . '/local-settings.inc';
}