<?php
use Boostack\Models\User\User;
use Boostack\Models\Config;
use Boostack\Models\Request;

$input = $_POST;
$error = FALSE;
$finalSetupMessageError = "";

if (empty($input['rootpath'])) {
    $error = TRUE;
    $finalSetupMessageError .= "Missing rootpath <br/>";
}
if (empty($input['url'])) {
    $error = TRUE;
    $finalSetupMessageError .= "Missing URL <br/>";
}
$env_parameters = [
    "current_environment" => "Environment::".$input['current_environment'],
    "rootpath" => $input['rootpath'],
    "url" => trim($input['url'], "/") . '/',
    'protocol' => $input['protocol'],
    'port' => $input['port'],
    'dn' => $input['dn'],
    "database_on" => $input['db-active'],
    "driver_pdo" => $input["driver-PDO"],
    "db_host" => $input['db-host'],
    "db_port" => $input['db-port'],
    "db_name" => $input['db-name'],
    "db_username" => $input['db-username'],
    "db_password" => $input['db-password'],
    "session_on" => $input['db-session-active'],
    'csrf_on' => $input['db-csrf-active'],
    "cookie_on" => $input['db-cookie-active'],
    "cookie_expire" => $input['db-cookie-expired'],
    "cookie_name" => $input['db-cookie-name'],
    "log_on" => $input['db-log-active'],
    "api_on" => $input['api-active'],
    "api_secret_key" => generateRandomStringApiKey(),
    "lockStrategy_on" => !empty($input["db-lockStrategy_on"])?$input["db-lockStrategy_on"]:FALSE,
    "login_max_attempts" => isset($input["db-loginLock-max-attempts"])?$input["db-loginLock-max-attempts"]:"3",
    "lockStrategy_type" => isset($input["db-loginLock-type"]) ? $input["db-loginLock-type"] : "timer",
    "recaptcha_public" => $input["db-loginLock-recaptcha-public"],
    "recaptcha_private" => $input["db-loginLock-recaptcha-private"],
    "login_seconds_blocked" => isset($input["db-loginLock-timer-seconds"])?$input["db-loginLock-timer-seconds"]:"180"
];

$exampleEnvName = "sample.env.php";
$exampleEnvPath = "/../setup/";
$outputEnvName = "env.php";
$envPath = "/../../config/env/";
$exampleEnvPath = realpath(__DIR__ . $exampleEnvPath) . "/" . $exampleEnvName; 
$finalEnvPath = realpath(__DIR__ . $envPath) . "/" . $outputEnvName;

$envContent = @file_get_contents($exampleEnvPath);
if ($envContent === FALSE) {
    $finalSetupMessageError = "message: setup/sample.env.php -> failed to open stream: Permission denied. <br/><br/>Solution: add read access to 'setup' folder";
} else {
    foreach ($env_parameters as $param => $value) {
        if (!is_string($param)) {
            continue;
        }
        if(is_null($value)) {print_r( $param);print_r( $value);}
        $value = ($value == "true" || $value == "false") ? strtoupper($value) : $value;
        $envContent = str_replace("[$param]", $value, $envContent);
    }
    $old = umask(0);
    if (@file_put_contents($finalEnvPath, $envContent) === FALSE) {
        $finalSetupMessageError = "message: config/env/env.php -> failed to open stream: Permission denied. <br/><br/>Solution: add write access to 'config/env' folder";
    }
}

if ($env_parameters["database_on"] == "true" && $finalSetupMessageError == "") {
    try {
        // require_once("../../config/env/env.php");
        // require_once("../../core/classes/Utils.Class.php");
        // spl_autoload_register('Utils::autoloadClass');
        require __DIR__ . '/../../vendor/autoload.php';
        Request::init();
        Config::init();
        $db = \Boostack\Models\Database\Database_PDO::getInstance($env_parameters["db_host"], $env_parameters["db_name"], $env_parameters["db_username"], $env_parameters["db_password"], $env_parameters["db_port"]);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $shouldImportDump = isset($_POST["db-dump-active"]) && $_POST["db-dump-active"] == "true";
        if ($shouldImportDump) {
            if (databaseHasTables($db, $env_parameters["db_name"])) {
                throw new \Exception(
                    "The selected database is not empty. Auto Import Dump can be used only with an empty database. " .
                    "Use an empty database or set 'Auto Import Dump' to 'No' to preserve the existing schema and data."
                );
            }

            $sql = file_get_contents('boostack_db.sql');
            $qr = $db->exec($sql);

            $users = array();
            $users[0] = "user@getboostack.com";
            $users[1] = "admin@getboostack.com";
            $users[2] = "superadmin@getboostack.com";
            foreach ($users as $user) {
                while (User::existsByEmail($user, false)) {
                    $id = User::getUserIDByEmail($user, false);
                    $toDelete = new User();
                    $toDelete->load($id);
                    $toDelete->delete();
                }
            }

            $u = new User();
            $u->username = "boostack";
            $u->name = "Boostack System";
            $u->email = "system@getboostack.com";
            $u->pwd = "testing";
            $u->privilege = "0";
            $u->first_name = "Boostack";
            $u->company = "Boostack";
            $u->last_name = "System";
            $u->save(1);

            $u = new User();
            $u->username = "boostackuser";
            $u->name = "Boostack User";
            $u->email = "user@getboostack.com";
            $u->pwd = "testing";
            $u->privilege = "3";
            $u->active = "1";
            $u->first_name = "Boostack";
            $u->company = "Boostack";
            $u->last_name = "User";
            $u->save(2);
            $u->createJWTToken();

            $u = new User();
            $u->username = "boostackadmin";
            $u->name = "Boostack Admin";
            $u->email = "admin@getboostack.com";
            $u->pwd = "testing";
            $u->privilege = "2";
            $u->active = "1";
            $u->first_name = "Boostack";
            $u->company = "Boostack";
            $u->last_name = "Admin";
            $u->save(3);
            $u->createJWTToken();

            $u = new User();
            $u->username = "boostacksuperadmin";
            $u->name = "Boostack SuperAdmin";
            $u->email = "superadmin@getboostack.com";
            $u->pwd = "testing";
            $u->privilege = "1";
            $u->active = "1";
            $u->first_name = "Boostack";
            $u->company = "Boostack";
            $u->last_name = "SuperAdmin";
            $u->save(4);
            $u->createJWTToken();
        }

    } catch (\PDOException $e) {
        $finalSetupMessageError = "Database Error. Message: " . $e->getMessage();
        unlink($finalEnvPath);
    } catch (\Exception $e2) {
        $finalSetupMessageError = "Error. Message: " . $e2->getMessage();
        unlink($finalEnvPath);
    }
}

function generateRandomStringApiKey($length = 32) {
    $length = max(32, (int) $length);
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function databaseHasTables(\PDO $db, string $dbName): bool
{
    $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :db_name";
    $query = $db->prepare($sql);
    $query->bindValue(':db_name', $dbName);
    $query->execute();

    return (int) $query->fetchColumn() > 0;
}
?>
