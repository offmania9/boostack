<?php

require_once "../core/environment_init.php";

if (!(Config::get('session_on') && Auth::isLoggedIn())) return false;

$res = new MessageBag();

try {
    $filterPage = Request::hasPostParam("filterPage") ? Request::getPostParam("filterPage") : null;
    $currentPage = Request::hasPostParam("currentPage") ? Request::getPostParam("currentPage") : 1;
    $perPage = Request::hasPostParam("perPage") ? Request::getPostParam("perPage") : 25;
    $orderBy = Request::hasPostParam("orderBy") ? Request::getPostParam("orderBy") : null;
    $orderType = Request::hasPostParam("orderType") ? Request::getPostParam("orderType") : null;
    $field = Request::hasPostParam("field") ? Request::getPostParam("field") : null;
    $input = Request::hasPostParam("input") ? Request::getPostParam("input") : null;
    $rule = Request::hasPostParam("rule") ? Request::getPostParam("rule") : null;
    $filters = Request::hasPostParam("filters") ? Request::getPostParam("filters") : null;

    if (!Validator::numeric($currentPage))
        throw new Exception("error currentpage");
    if (!Validator::numeric($perPage))
        throw new Exception("error perpage");
    if (!Validator::onlyCharNumbersUnderscore($orderBy))
        throw new Exception("error orderby");
    if (!(!empty($orderType) && ($orderType == 'ASC' || $orderType == 'DESC')))
        throw new Exception("error orderType");

    $fieldViewArray = array();

    if (empty($filters)) { //single filter
        $field = Request::hasPostParam('field') && !empty(Request::getPostParam("field")) ? Request::getPostParam("field") : $error = "field not valid";
        $input = Request::hasPostParam("input") ? Request::getPostParam("input") : $error = "input not valid";
        $rule = Request::hasPostParam('rule') && !empty(Request::getPostParam("rule")) ? Request::getPostParam("rule") : $error = "rule not valid";
        if (!Validator::onlyCharNumbersUnderscore($field))
            throw new Exception("error field");
        if (!(Validator::alphanumeric($input) || Validator::numeric($input)))
            throw new Exception("error input");
        if (!Validator::operators($rule))
            throw new Exception("error rule");
        $fieldViewArray[] = array($field, $rule, $input);
    } else { //multiple filter
        foreach ($filters as $filter) {
            if (!Validator::onlyCharNumbersUnderscore($filter["field"]))
                throw new Exception("error field");
            if (!(Validator::alphanumeric($filter["input"]) || Validator::numeric($filter["input"])))
                throw new Exception("error input");
            if (!Validator::operators($filter["rule"]))
                throw new Exception("error rule");
            $fieldViewArray[] = array($filter["field"], $filter["rule"], $filter["input"]);
        }
    }

    if ($filterPage != null) {
        switch ($filterPage) {
            case "logList":
                $logList = new Log_Database_List();
                $data = $logList->view($fieldViewArray, $orderBy, $orderType, $perPage, $currentPage);
                saveFilterDataInSession("filter_log", $fieldViewArray,$orderBy,$orderType,$perPage,$currentPage);
                $result["totalitem"] = $data;
                $result["items"] = $logList->getItemsArray();
                ($data != null) ? $res->setData($result) : $res->setError("Nessun dato disponibile");
                break;
            case "userList":
                $fieldViewArray[] = array("privilege", ">=", Auth::getUserLoggedObject()->privilege);
                $usersList = new UserList();
                $data = $usersList->view($fieldViewArray, $orderBy, $orderType, $perPage, $currentPage);
                saveFilterDataInSession("filter_users", $fieldViewArray,$orderBy,$orderType,$perPage,$currentPage);
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
} catch(Exception_Validation $ev){
    $res->setError($e->getMessage());
    Logger::write('Validation exception: '.$e,Log_Level::ERROR);
} catch (Exception $e) {
    $res->setError($e->getMessage());
    Logger::write('request not valid'.$e,Log_Level::ERROR);
}

echo $res->toJSON();

function saveFilterDataInSession($type, $fieldViewArray, $orderBy, $orderType, $perPage, $currentPage) {
    foreach($fieldViewArray as &$fields) {
        $fields[1] = htmlspecialchars_decode($fields[1]);
    }
    $data = [
        "fields" => $fieldViewArray,
        "orderBy" => $orderBy,
        "orderType" => $orderType,
        "perPage" => $perPage,
        "currentPage" => $currentPage,
    ];
    Session::set($type, $data);
}
?>