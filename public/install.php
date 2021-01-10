<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

/**
 * This script:
 * - Creates all tables
 * - Inserts user login
 * - Inserts PitonCMS/Engine version
 * - Redirects to login page
 * - Deletes this script
 */

// Error reporting wide open
ini_set('display_errors', 'On');
error_reporting(-1);

// Set encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

define('ROOT_DIR', dirname(__DIR__) . '/');

function throwPitonError($message)
{
    $title = 'PitonCMS Installation Error';
    $html = "<p>$message</p>";

    http_response_code(500);
    echo sprintf(
        "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
        "<title>%s</title><style>body{margin:0;padding:30px;font:14px Helvetica,Arial,Verdana," .
        "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
        "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
        $title,
        $title,
        $html
    );

    exit;
}

// Install database if this script is called via POST Request
if (isset($_POST['submit'])) {
    // Make sure at a minimum that we have the user email address
    if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throwPitonError("You must provide a valid email address. {$_POST['email']} is not valid.");
    }

    // Get data base credentials from config file
    if (file_exists(ROOT_DIR . 'config/config.local.php')) {
        require ROOT_DIR . 'config/config.local.php';
    } else {
        throwPitonError("No config/config.local.php file found.");
    }

    // Make sure we have details to connect to the DB
    if (
        (!isset($config['database']['host']) || empty($config['database']['host'])) ||
        (!isset($config['database']['dbname']) || empty($config['database']['dbname'])) ||
        (!isset($config['database']['username']) || empty($config['database']['username'])) ||
        (!isset($config['database']['password']) || empty($config['database']['password']))
    ) {
        throwPitonError("Configuration database values are not all set in config/config.local.php. Edit config/config.local.php.");
    }

    // Get the pitoncms/engine version from composer.lock file, this is the stored token each page view will check
    if (null === $definition = json_decode(file_get_contents(ROOT_DIR . 'composer.lock'))) {
        throwPitonError("Unable to read PitonCMS/Engine version from composer.lock.");
    }
    $engineKey = array_search('pitoncms/engine', array_column($definition->packages, 'name'));
    $engineVersion = $definition->packages[$engineKey]->version ?? 'dev';

    // Setup database config
    $dbConfig = $config['database'];
    $dbConfig['options'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $dbConfig['options'][PDO::ATTR_EMULATE_PREPARES] = false;

    // Define connection string
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";

    // Return connection
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);

    // Verify tables do not exist before installation
    try {
        $result = $pdo->query('select 1 from `page` limit 1');
        if ($result) {
            throw new Exception("exists");
        }
    } catch (Exception $e) {
        // The SQLSTATE code '42S02' means table does not exist - which is good so we can ignore that
        if ($e->getCode() !== '42S02') {
            throwPitonError("Database already installed. Stopping install.");
        }
    }

    // Get install script
    if (file_exists(ROOT_DIR . 'vendor/pitoncms/engine/schema/build.sql')) {
        $script = file_get_contents(ROOT_DIR . 'vendor/pitoncms/engine/schema/build.sql');
    } else {
        throwPitonError("Unable to find table install script.");
    }

    // Run script
    try {
        $pdo->exec($script);
    } catch (Exception $e) {
        throwPitonError("Exception running install script: {$e->getMessage()}.");
    }

    // Insert user
    $insertNewUser = 'insert into `user` (`first_name`, `last_name`, `email`, `role`, `created_date`, `updated_date`) values (?, ?, ?, ?, ?, ?);';
    $userData[] = $_POST['firstName'] ?? null;
    $userData[] = $_POST['lastName'] ?? null;
    $userData[] = $_POST['email'];
    $userData[] = 'A';
    $userData[] = date('Y-m-d H:i:s');
    $userData[] = date('Y-m-d H:i:s');

    // Insert siteName into data_store
    $insertSiteName = 'insert into `data_store` (`category`, `setting_key`, `setting_value`, `created_date`, `updated_date`) values (?, ?, ?, ?, ?);';
    $dataSiteName[] = 'site';
    $dataSiteName[] = 'siteName';
    $dataSiteName[] = $_POST['siteName'] ?? null;
    $dataSiteName[] = date('Y-m-d H:i:s');
    $dataSiteName[] = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare($insertNewUser);
        $stmt->execute($userData);

        $stmt = $pdo->prepare($insertSiteName);
        $stmt->execute($dataSiteName);
    } catch (Exception $e) {
        throwPitonError("Failed to insert user or site data: {$e->getMessage()}.");
    }

    // Set engine version as key to avoid running install again
    $insertEngineSetting = 'update `data_store` set `setting_value` = ?, `updated_date` = ? where `category` = \'piton\' and  `setting_key` = \'engine\';';
    $settingValue[] = $engineVersion;
    $settingValue[] = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare($insertEngineSetting);
        $stmt->execute($settingValue);
    } catch (Exception $e) {
        throwPitonError("Failed to insert engine setting: {$e->getMessage()}.");
    }

    // Redirect to login page
    header("Location: /login", true, 302);

    // Self destruct
    unlink(__FILE__);
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="PitonCMS">
  <link rel="icon" href="">

  <title>PitonCMS Install</title>

</head>

<body>
  <header>
    <h1>PitonCMS</h1>
  </header>
  <div class="loginPage">
    <h2>Install Database</h2>
    <form class="form-signin" action="/install.php" method="post" accept-charset="utf-8">

        <p>
            <label>First Name</label>
            <input type="text" name="firstName" maxlength="60" placeholder="First name">
        </p>

        <p>
            <label>Last Name</label>
            <input type="text" name="lastName" maxlength="60" placeholder="Last name">
        </p>

        <p>
            <label>Email Address*</label>
            <input type="email" name="email" maxlength="60" placeholder="Email" required>
        </p>

        <p>
            <label>Website Name<label>
            <input type="text" name="siteName" maxlength="60" placeholder="Website name">
        </p>

        <button type="submit" name="submit">Install Database</button>

    </form>
  </div>

</body>

</html>