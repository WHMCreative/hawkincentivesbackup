<?php

/**
 * Drupal VM drush aliases.
 *
 * @see example.aliases.drushrc.php.
 */

$aliases['bhk.drupalvm'] = array(
  'uri' => 'bhk.drupalvm',
  'root' => '/var/www/bhk/docroot',
  'remote-host' => 'bhk.drupalvm',
  'remote-user' => 'vagrant',
  'ssh-options' => '-o "SendEnv PHP_IDE_CONFIG PHP_OPTIONS XDEBUG_CONFIG" -o PasswordAuthentication=no -i "' . (getenv('VAGRANT_HOME') ?: drush_server_home() . '/.vagrant.d') . '/insecure_private_key"',
  'path-aliases' => array(
    '%drush-script' => '/usr/local/bin/drush',
  ),
);

$aliases['www.bhk.drupalvm'] = array(
  'uri' => 'www.bhk.drupalvm',
  'root' => '/var/www/bhk/docroot',
  'remote-host' => 'www.bhk.drupalvm',
  'remote-user' => 'vagrant',
  'ssh-options' => '-o "SendEnv PHP_IDE_CONFIG PHP_OPTIONS XDEBUG_CONFIG" -o PasswordAuthentication=no -i "' . (getenv('VAGRANT_HOME') ?: drush_server_home() . '/.vagrant.d') . '/insecure_private_key"',
  'path-aliases' => array(
    '%drush-script' => '/usr/local/bin/drush',
  ),
);



$aliases['prod'] = array(
  'root' => '/var/www/bhk-d8.prod.e3develop.com/htdocs',
  'uri' => 'bhk-d8.prod.e3develop.com',
  'remote-host' => '199.167.77.119',
  'remote-user' => 'hawk1',
  'path-aliases' => array(
    '%files' => '/mnt/gluster/files/bhk-d8.prod.e3develop.com/files',
    '%dump-dir' => '/home/hawk1/tmp'
  ),
  'command-specific' => array(
    'sql-sync' => array(
      'no-cache' => TRUE,
    ),
  ),
);

$aliases['prod2'] = array(
  'root' => '/var/www/bhk-d8.prod.e3develop.com/htdocs',
  'uri' => 'bhk-d8.prod.e3develop.com',
  'remote-host' => '199.167.77.121',
  'remote-user' => 'hawk1',
  'path-aliases' => array(
    '%files' => '/mnt/gluster/files/bhk-d8.prod.e3develop.com/files',
    '%dump-dir' => '/home/hawk1/tmp'
  ),
  'command-specific' => array(
    'sql-sync' => array(
      'no-cache' => TRUE,
    ),
  ),
);

$aliases['dev'] = array(
  'root' => '/var/www/bhk-d8.dev.e3develop.com/htdocs',
  'uri' => 'bhk-d8.dev.e3develop.com',
  'remote-host' => '199.167.77.120',
  'remote-user' => 'hawk1',
  'path-aliases' => array(
    '%files' => 'sites/default/files',
    '%dump-dir' => '/home/hawk1/tmp'
  ),
  'command-specific' => array(
    'sql-sync' => array(
      'no-cache' => TRUE,
    ),
  ),
);

