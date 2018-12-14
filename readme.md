# Blackhawk Incentives

## Hosting

* Platform: BlackMesh/Contegix
* Dev URL: bhk-d8.dev.e3develop.com
* Prod URL: bhk-d8.dev.e3develop.com
* Live URL: hawkincentives.com

The site is hosted on Contegix with three servers (MP1, MP2, MP3). All server creds are stored in LastPass.

As of July, 2018, the account with Contegix is owned by Elevated Third and is called Elevated Third - Hawak Incentives. 

MP1 & MP2 are the production servers that are then then servered up through a load balancer. 

MP3 is used as the dev server. 

### Shield credentials

* Username: hawkincentives
* Password: 3ditHawk


## Deploying

This site is using a custom version of deploykit ( based on https://github.com/elevatedthird/deploykit/tree/d8 ). Deploykit has been customized to work with a load balancer. 

To deploy to dev run from deploykit directory `make deploy env=dev` and provide the system password.

To deploy to prod run from deploykit directory `make deploy env=mp1 && make deploy env=mp2` to make simultaneously deployments to MP1 and MP2 and provide the system passwords for each server respectively.

## Caching & Varnish

There is a layer of caching on the servers – Varnish purge has been configured to asset with this. Each server needs to be setup to point to its own varnish to work. This is configured in the `settings.php` file and uses a server variable to switch accordingly.  
* MP1 = 172.28.4.1
* MP2 = 172.28.4.2
* MP3 = 172.28.4.3

To clear the varnish cache on a particular server run the command `systemctl restart varnish.service` or `service varnish restart`. In the case of clearing varnish cache, this command will need to be run on both MP1 & MP2.

Drupal cache will also need to be cleared on each server independently. Drush aliases have been established for this reason ( @bhk.prod & @bhk.prod2). 

## Config workflow

This project uses CMI for all configuration changes. To make a configuration
update, first, sync the production database to your local environment and export and commit any config changes from the production environment to the project repo. After that, make your new configuration changes on your local environment, export those changes and commit them to the project repo. Once you're finished push those changes to the remote environment and run `drush cim vcs` to import all new configuration.


## Composer

All packages in this project are managed using Composer - see composer.json for detailed information about each package. This project is not being built serverside, so the vendor directory is committed - be sure any new packages you add are committed to this repo.