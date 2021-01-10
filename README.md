# PitonCMS
Friendly and Flexible Content Management System

## A Designer Friendly CMS
PitonCMS was designed to be designer friendly, giving great flexibility to the designer to build creative websites without requiring additional custom backend development.

Page structures, data fields, settings, are all easily extensible by modifying project JSON **Definition** files. These files can be checked into version control and pushed to other environments to promote layout and data changes without having to modify remote databases or push code.

PitonCMS is built on modern standards and packages:
* PHP 7.1+
* Runs on the [Slim](http://www.slimframework.com/) framework, fast and powerful
* Coded to PHP-FIG [PSR-12](https://www.php-fig.org/psr/psr-12/) standards
* [Composer](https://getcomposer.org/) for package management
* [Twig](https://twig.symfony.com/) to render HTML templates

## Requirements
* *AMP environment with PHP 7.1 or greater. PitonCMS comes with a [Docker-Compose](https://docs.docker.com/compose/) image ready to run for local development
* [Composer](https://getcomposer.org/) installed on your development environment

## Install PitonCMS
To install PitonCMS using composer from the command line, change directories (`cd`) to where you want to create your new PitonCMS project directory, and run the composer `create-project` command, but update `<my-project-name>` to the new project name (to use Docker ensure the project name is all lower case):

```
composer create-project pitoncms/piton <my-project-name>
```

This will create a new instance of PitonCMS in a folder of the same name. When composer asks if you wish to delete any version control files say yes.

The `create-project` command will automatically run a post create script that:

* Updates the Docker Compose YAML files and Apache config files for your project
* Copies the `config/config.default.php` file to `config/config.local.php`, and sets appropriate local _development_ environment settings

## Run PitonCMS in Docker Container
If you have a [Docker](https://docs.docker.com/) client installed on your computer, from the command line `cd` into the new project folder and run:

```
docker-compose build
```
to build the Docker images. This is a one time step.

To start the Docker container and webserver, run:

```
docker-compose up -d
```

To later stop your container, run:

```
docker-compose stop
```

If you do not have the Docker client, then run PitonCMS on your local *AMP server.

After starting your development server, open a browser and go to `http://localhost`. The first time you will be directed to the installer script. Enter your name and email address and click submit to build the database and add you as an administrative user.

**Note:** The installer script (`public/install.php`) deletes itself after creating the database. If for some reason the self-delete fails, be sure to manually delete this file. DO NOT commit `install.php` and/or push to a production environment.

## Login to PitonCMS
PitonCMS does not store passwords. PitonCMS uses passwordless authentication by email.

To login, navigate to `/login` and enter the email address you used during the install. You will be sent a one-time use hashed token with a login link that is good for 15 minutes.

Sessions are valid for the duration set in the `secondsUntilExpiration` session configuration item, which defaults to two hours from the last request. This can be increased to any session length.

## Inside PitonCMS Administration
After logging in to the PitonCMS administration back end, go ahead and explore. A good first stop is under the **Tools** menu is to review the client controlled site settings. Also review the **Support** documentation for Designer and Client.

## First Project Commit
For your project using PitonCMS, before your first commit you should:
* Make sure that `public/install.php` has been deleted
* Consider whether to also remove `vendor/` from `.gitignore`

There are various schools of thought on weather to commit `vendor` folders in your project. If you commit your `composer.lock` you _should_ be able to install the same critical files by running `composer install` from the project root on another environment. But committing those same files assures you of maintaining the same file versions as well.

## Backend Configuration Settings
After installing the project, inspect the `config/config.local.php` configuration file (which was copied from `config.default.php` as part of the composer create project step) to ensure the configuration values are set for your environment.

Do not update the `config.default.php` settings directly as those override the default file, make any desired changes in `config.local.php`.

**DO NOT** commit `config.local.php` to your version control system as it holds critical passwords and keys. For any instance of your PitonCMS project, you will need a local  `config.local.php` file to manage local server settings.

### Configuration Setting Options

#### Production Environment Flag
The `production` configuration setting flag should be set for your local environment. If set to `true` then error details are suppresed and sent to the server logs, not the screen.

When developing a website on PitonCMS with `production` set to `false`, the Twig cache will be automatically cleared when templates change, which helps the development process. However, this is not the case when set to `true`, in that environment the `cache/` directory will need to be manually cleared when releasing template changes.

This should always be set to `true` in production.

```php
/**
 * Production
 * Boolean variable controls debug and environment modes
 * Set to false in config.local.php on a development environment.
 */
$config['site']['production'] = true;
```

#### Database
If using Docker for your local development environment the database connection credentials should have been set as part of the install process. For production, enter your production database credentials.

```php
/**
 * Database
 */
$config['database']['host'] = 'localhost';
$config['database']['dbname'] = '';
$config['database']['username'] = '';
$config['database']['password'] = '';
```

If using Docker for your local development environment the session settings should have been set as part of the install process. For production, be sure to set your desired `cookieName` and a suitably long and complex `salt` hash.

#### Session
```php
/**
 * Sessions
 *
 * Set 'salt' to a long hash.
 */
$config['session']['cookieName'] = 'pitoncms';
$config['session']['checkIpAddress'] = true;
$config['session']['checkUserAgent'] = true;
$config['session']['salt'] = '';
$config['session']['secondsUntilExpiration'] = 7200;
```

#### Email
PitonCMS relies by default on your local `mail` client, but if that is not installed or your local ISP blocks port 25 then add your SMTP credentials.

```php
/**
 * Email
 *
 * from:     Send-from email address
 * protocol: 'mail' (default) or 'smtp'
 *
 * These settings below only apply for SMTP connections
 * smtpHost: SMTP server name
 * smtpUser: User name
 * smtpPass: Password
 * smtpPort: Port to use, likely 465
 */
$config['email']['from'] = 'pitoncms@localhost.com';
$config['email']['protocol'] = 'mail';
$config['email']['smtpHost'] = '';
$config['email']['smtpUser'] = '';
$config['email']['smtpPass'] = '';
$config['email']['smtpPort'] = '';
```

#### Pagination Links
If you have many pages or media files, pagination controls will appear to manage the number of records on screen.

```php
/**
 * Pagination Row Limits
 */
$config['pagination']['adminPagePagination']['resultsPerPage'] = 6;
$config['pagination']['adminMediaPagination']['resultsPerPage'] = 10;
```

## Updating PitonCMS
The PitonCMS system relies on a vendor package named `pitoncms/engine` which has the core PitonCMS code and files. To update PitonCMS, from the terminal in the root of your project run:

```bash
composer update pitoncms/engine
```
to get the latest version, and then commit the `composer.lock` file (and possibly commit the `vendor` folder).

**Note**: You should run the update command from your live webserver or inside the Docker container, as the update executes a script to set the Engine release number in the database, which needs to be running. To access the Docker container:

```bash
docker exec -it piton_web_1 bash
cd /var/www/piton
composer update pitoncms/engine
```
Where you replace `piton` with your project name.

The PitonCMS frontend project files above the `vendor` folder are never updated, so your custom files are never touched.
