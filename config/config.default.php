<?php
/**
 * Default Configuration Settings
 *
 * DO NOT MODIFY THIS FILE
 * Copy this file to config.local.php to define all environment specific settings.
 */

/**
 * Production Environment Flag
 *
 * Boolean variable controls debug and environment modes.
 * Always set to true in production.
 * Set to false in config.local.php for development.
 */
$config['environment']['production'] = true;

/**
 * Database
 *
 * Database configuration settings
 * - host:     Database server
 * - dbname:   Database name
 * - username: Database user name
 * - password: Database password
 */
$config['database']['host'] = 'localhost';
$config['database']['dbname'] = '';
$config['database']['username'] = '';
$config['database']['password'] = '';

/**
 * Session
 *
 * Session management settings
 * - cookieName:             Application cookie name
 * - checkIpAddress:         Whether to verify request IP address
 * - checkUserAgent:         Whether to verify the request useragent string
 * - salt:                   Complex string to hash for session ID
 * - secureCookie:           Set to true if HTTPS, false if HTTP
 * - secondsUntilExpiration: How many seconds to set the session on each request, defaults to 2 hours.
 *      Can accept expression such as 60*60*24;
 */
$config['session']['cookieName'] = 'pitoncms';
$config['session']['checkIpAddress'] = true;
$config['session']['checkUserAgent'] = true;
$config['session']['salt'] = '';
$config['session']['secureCookie'] = true;
$config['session']['secondsUntilExpiration'] = 7200;

/**
 * Email
 *
 * Email configuration settings. Defaults to sendmail
 * - from:     Send-from email address
 * - protocol: 'mail' (default) or 'smtp'
 *
 * These settings only apply for SMTP connections
 * - smtpHost: SMTP server name
 * - smtpUser: User name
 * - smtpPass: Password
 * - smtpPort: Port to use, likely 465
 */
$config['email']['from'] = 'pitoncms@localhost.com';
$config['email']['protocol'] = 'mail';
$config['email']['smtpHost'] = '';
$config['email']['smtpUser'] = '';
$config['email']['smtpPass'] = '';
$config['email']['smtpPort'] = '';

/**
 * Pagination
 *
 * Set default number of results to return in pagination queries.
 * Affects both administration and front end pagination.
 */
$config['pagination']['resultsPerPage'] = 20;
