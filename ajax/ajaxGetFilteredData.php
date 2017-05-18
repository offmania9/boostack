<?php

require_once "../core/environment_init.php";

if (!(Config::get('session_on') && Auth::isLoggedIn())) return false;

$res = new MessageBag();

try {
    $filterPage = Request::hasPostParam("filterPage") ? Request::getPostParam("filterPage") : null;
    $result = array();
    $fieldViewArray = array();
    $currentPage = Request::hasPostParam("currentPage") ? Request::getPostParam("currentPage") : 1;
    $perPage = Request::hasPostParam("perPage") ? Request::getPostParam("perPage") : 10;
    $orderBy = Request::hasPostParam("orderBy") ? Request::getPostParam("orderBy") : null;
    $orderType = Request::hasPostParam("orderType") ? Request::getPostParam("orderType") : null;
    $field = Request::hasPostParam("field") ? Request::getPostParam("field") : null;
    $input = Request::hasPostParam("input") ? Request::getPostParam("input") : null;
    $rule = Request::hasPostParam("rule") ? Request::getPostParam("rule") : null;
    $currentUser = Auth::getUserLoggedObject();
    if ($filterPage != null) {
        switch ($filterPage) {
            case "userList":
                $usersList = new User_List();
                $fieldViewArray[] = array($field, $rule, $input);
                $fieldViewArray[] = array("privilege", ">=", $currentUser->privilege);
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
    Logger::write('request not valid'.$e->getMessage(),Log_Level::ERROR);
}

echo $res->toJSON();
?>