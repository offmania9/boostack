<?php

require_once "../core/environment_init.php";

if (!(Config::get('session_on') && Auth::isLoggedIn())) return false;

$res = new MessageBag();

try {
    $filterPage = (!empty($_POST["filterPage"])) ? Utils::sanitizeInput($_POST["filterPage"]) : null;
    $result = array();
    $fieldViewArray = array();
    $currentPage = (!empty($_POST["currentPage"])) ? Utils::sanitizeInput($_POST["currentPage"]) : 1;
    $perPage = (!empty($_POST["perPage"])) ? Utils::sanitizeInput($_POST["perPage"]) : 10;
    $orderBy = (!empty($_POST["orderBy"])) ? Utils::sanitizeInput($_POST["orderBy"]) : null;
    $orderType = (!empty($_POST["orderType"])) ? Utils::sanitizeInput($_POST["orderType"]) : null;
    $field = (!empty($_POST["field"])) ? Utils::sanitizeInput($_POST["field"]) : null;
    $input = (isset($_POST["input"])) ? Utils::sanitizeInput($_POST["input"]) : null;
    $rule = (!empty($_POST["rule"])) ? Utils::sanitizeInput($_POST["rule"]) : null;
    if ($filterPage != null) {
        switch ($filterPage) {
            case "userList":
                $usersList = new User_List();
                $fieldViewArray[] = array($field, $rule, $input);
                $fieldViewArray[] = array("privilege", ">=", $CURRENTUSER->privilege);
                $data = $usersList->view($fieldViewArray,$orderBy,$orderType,$perPage,$currentPage);
                $result["totalitem"] = $data;
                $result["items"] = $usersList->getItemsArray();
                ($data != null) ? $res->setData($result) : $res->setError("Nessun dato disponibile");
                break;
            default:
                $res->setError("Nessuna azione");
                break;
        }
    } else {
        $res->setError("Nessuna azione disponibile");
    }
} catch (Exception $e) {
    $res->setError($e->getMessage());
    $boostack->writeLog('request not valid'.$e->getMessage(),"error");
}

echo $res->toJSON();
?>