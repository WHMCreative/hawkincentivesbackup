DeployKit
---------
Ansible based Drupal deployment kit

### Contents
- **Installation**
  - [Requirements](#requirements)
  - [Setup](#setup)
  - [Converting an existing site](#converting-an-existing-site)
- **Usage**
  - [Basic Usage](#basic-usage)
  - [Using DeployKit without `sudo`](#sudo-less-deploys)
  - [Advanced Usage](#advanced-usage)
- **Project Docs**
  - [Why use DeployKit?](#why-use-deploykit)
  - [Project Structure](#project-structure)
  - [Tags](#tags)

## Installation

### Requirements
You will need to have [Ansible](http://docs.ansible.com/intro_installation.html) installed. Ansible itself requires python.

### Setup
Before deploying, you will need to set up some site specific variables and host information. These settings live outside the deploykit directory in a separate directory named `settings`. The required files are:

    hosts
    <env>.yml (where <env> == dev, stg, prd, etc.)
    deploykit.conf

If you are also using [StarterKit](https://github.com/elevatedthird/starterkit), these should already be provisioned by its `make init` command. If you are not, example files for these are provided in this repo.

First, edit the hosts file and add any IP addresses for dev, staging, and/or production servers.

Deploykit automatically connects the passed `env` variable to an associated environment yaml file. For example, running `make setup env=dev` will use the `dev.yml` file in the settings directory, while `make setup env=long_sentence` will use `long_sentence.yml`. All of these yaml files use the same variables.

In each of the YAML files, you will need to add variables like server users, site name, git repo, etc. before deploying to that environment. Once you've done this, you can follow the basic [steps below](#basic-usage).

There is a `deploykit.conf.example` file available. Copy it into your project settings directory so that it exists as `../settings/deploykit.conf`.

### Converting an existing site

Beyond the files needed and listed above, a few changes need to be made to the site repo to take advantage of DeployKit. Those are:

    settings.php
    .gitignore
    docroot/.gitignore

##### settings.php
DeployKit requires a settings.php file to be committed to the project as DeployKit will automatically create a `local-settings.inc` file on the remote host for you, so you do not need to commit credentials to the settings file. In order for Drupal to read this local-settings.inc file, the `settings.php` file needs to include it. DeployKit will also set a few environment variables on the dev/stage/prod server that allow you to specify different configuration per environment. Again, the `settings.php` file needs to know to look for these.

Therefore, you will need to add and commit `settings.php` to the repo. A template settings.php is available in the StarterKit repo as [default.settings.php](https://github.com/elevatedthird/starterkit/blob/master/default.settings.php). Just copy that into sites/default and the take any special configuration from develop/staging/production.settings.php and put those in the switch statement beginning around [line 569](https://github.com/elevatedthird/starterkit/blob/master/default.settings.php#L569).

**Gotchas** - Don't forget that when deploying, the settings file needs to be committed to the appropriate branch. It's often forgotten to add settings.php via a release to `master`. 

##### .gitignore
The `settings.php` file was previously always ignored, however DeployKit needs it to be committed. You will need to remove `*settings*.php` from the project `.gitignore` file so that it can be committed. However, Git will continue to ignore previously ignored files even after they are removed. The fix is to explicitly add your `settings.php` file to be committed with the command below.

    git add -f sites/default/settings.php # Note the -f flag

##### docroot/.gitignore
Drupal itself comes with a `.gitignore` file that may be committed to the repo and will ignore `settings.php`. You will need to remove this if it exists and add `docroot/.gitignore` to the project `.gitignore` so that it will not be added again on the next core update.

##### Media Temple
Deploykit works fine on Media Temple but there are a couple of things to note.

1. You must install the simplejson python package on the remote server. You can do so with the following command: `sudo yum install python-simplejson`
1. Deploykit won't be able to create a vhosts entry since that is all managed through plesk. Because of this, the `AH_SITE_SERVER` and `AH_SITE_ENVIRONMENT` variables will not be available. 

## Usage

### Basic Usage
To set up a site, you will run the following from the deploykit directory:

    make setup env=* # where * is equal to the environment yaml file

To deploy, you will run:

    make deploy env=* # where * is equal to the environment yaml file

Finally, to launch a site, you will run:

    make launch env=* # where * is equal to the environment yaml file

If you need to revert in a pinch, that's just as easy, run:

    make revert env=*

If you need to reprovision settings files (if they have changed) run:
    
    make reprov env=*

### `sudo`-less deploys
To enable `sudo`-less deployments, make sure your `deploykit.conf` has the following lines.

```make
flags=$(flags_default) --skip-tags sudo
#flags=$(flags_default) --ask-become-pass
```

This will ensure that any `sudo` tasks are skipped and that DeployKit won't ask for the `sudo` password.

**Remember**: You will still need to go through the whole deploy process at least once *with* `sudo` enabled. This only allows for *ongoing* deploys without using `sudo`.

### Advanced Usage
From the ansible directory, you will run:

    ansible-playbook <env>.yml

You may only wish to do portions of the provisioning process. To only execute a single stage of the process, you can use the `-t` flag:

    ansible-playbook <env>.yml -t setup # this would only create the directory structure

You may also exclude portions of the process with `--skip-tags`:

    ansible-playbook <env>.yml --skip-tags launch # Does everything but symlink ht(s)docs to app/current

## Project Docs

### Why use DeployKit?
- Start to finish
  - Creates vhosts, databases, and db users
  - Create local-settins.inc files per environment for you
- 2 commands to launch a site
  - `ansible-playbook <env>.yml`
  - `drush sql-sync @alias @alias`
- Declaritive syntax
  - Easy to reason about what is happening
  - Easy to customize
  - No more ruby or gem dependencies
  - YAML based, so in line w/ Drupal 8
- Idempotent
  - Run it as many times as you like
  - Run it to fix directory structure
  - Run it to fix permissions
- Fast
- Concise
- Gets us closer to being able to do across the board core updates
- Follows the same directory pattern as Cap
- Backwards compatible
  - Replace the cap directory w/ this, and set it up.
  - In theory, shouldn't break deployments going forward (needs testing)

### Project Structure
The entry point begins in the top level YAML file. There should be one file per environment. Each file specifies roles to provision. This set-up has 3 roles: apache, mysql, and application. Within each roles, there may be 3 dirs: templates, tasks, and handlers.

- Roles
  - apache
    - Sets up the vhost, makes sure apache is reloaded whenever the vhost is updated
  - mysql
    - Creates the database, user, and sets the users passwod
  - application
    - Sets up the file structure
    - Pulls the repo
    - Creates a release
    - Sets up symlinks
    - Writes a local-settings.inc file
- Templates
  - Currently, there's vhost.j2 and local-settings.j2 (python's template extension)
  - vhost.j2 will write out a correct vhost file based on the variables in the top-level YAML file
  - local-settings.j2 will write out a local-settings file based on the variables in the top-level YAML file
- Tasks
  - Executed sequentially, starting with main.yml.
  - application/tasks/main.yml uses includes to break apart files, loosely based on duties
  - Some tasks may have a `notify` setting. This means that if that task creates a change on the system, it will notify a handler (see below) to be triggered when all tasks are done. For example, this is used by the apache role vhost task to reload apache if and when the vhost is altered, but not when the vhost remains the same.
- Handlers
  - Handlers are just like tasks, but they will not be run unless notified (see above) by a task.

### Tags
You may notice that many of the tasks define `tags`. These tags allow you to run specific parts of the site provisioning process independently. For example, if you want to provision a site, but not launch it on prod, you would run `ansible-playbook prd.yml --skip-tags launch`. Or, if a site was already launched and you wanted to deploy code changes, but not yet release it, you would run `ansible-playbook prd.yml -t deploy`.

##### Available tags
- setup
- deploy
- release
- revert
- launch
