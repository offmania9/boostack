<?php
/**
 * Boostack: BaseList.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.3
 */
abstract class BaseList implements IteratorAggregate, JsonSerializable {

    protected $items;
    protected $pdo;
    protected $baseClassName;
    protected $baseClassTablename;

    /** List items object class */
    const BASE_CLASS = "";

    const ORDER_ASC = "ASC";
    const ORDER_DESC = "DESC";

    protected function init() {
        $this->pdo = Database_PDO::getInstance();
        $this->items = [];
        $this->baseClassName = static::BASE_CLASS;
        $this->baseClassTablename = (new $this->baseClassName)->getTablename();
    }

    /**
     * With this method you can iterate the list like an array
     * e.g. foreach($myList as $elem) ...
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    public function size() {
        return count($this->items);
    }

    protected function isEmpty() {
        return count($this->items) == 0;
    }

    protected function add($element) {
        $this->items[] = $element;
    }

    /**
     * This method is used when json_encode() is called
     * It expose "items" to the json_encode() function
     */
    public function jsonSerialize() {
        return $this->items;
    }

    protected function haskey($key) {
        // TODO
        return true;
    }

    protected function remove($key, $shift = true) {
        // TODO
        return true;
    }

    protected function get($key) {
        return $this->items[$key];
    }

    protected function find($field,$value) {
        // TODO
        return true;
    }

    protected function fill($array) {
        foreach ($array as $elem) {
            $baseClassInstance = new $this->baseClassName;
            $baseClassInstance->fill($elem);
            $this->items[] = $baseClassInstance;
        }
    }

    protected function loadAll() {
        $sql = "SELECT * FROM " . $this->baseClassTablename;
        $q = $this->pdo->prepare($sql);
        $q->execute();
        $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
        $this->fill($queryResults);
        $countResult = count($queryResults);
        return $countResult;
    }

    public function view($fields = NULL, $orderColumn = NULL, $orderType = NULL, $numitem = 25, $currentPage = 1) {
        $sql = "";
        $orderType = strtoupper($orderType);

        $error = false;
        if (!is_numeric($numitem)) $error = "Wrong num_item type";
        if (!is_numeric($currentPage) || $currentPage < 0) $error = "Wrong current_page format";
        if (!($orderType == self::ORDER_ASC || $orderType == self::ORDER_DESC)) $error = "Wrong order_type format";
        if (!(is_array($fields) && count($fields) > 0)) $error = "Wrong field_view format";
        if ($error !== false) throw new Exception($error);

        $sqlCount = "SELECT count(id) FROM " . $this->baseClassTablename . " ";
        $sqlMaster = "SELECT * FROM " . $this->baseClassTablename . " ";

        $sql .= "WHERE" . " ";
        foreach ($fields as $option) {
            if ($option[0] == "datetime") {
                $sql .= "FROM_UNIXTIME(" . $option[0] . ") ";
            } else
                $sql .= $option[0] . " ";
            $option[1] = strtoupper($option[1]);
            switch ($option[1]) {
                case '&LT;&GT;': {
                    $sql .= "!= '" . $option[2] . "'";
                    break;
                }
                case 'LIKE': {
                    $sql .= $option[1] . " '%" . $option[2] . "%'";
                    break;
                }
                case '=': {
                    $sql .= $option[1] . " '" . $option[2] . "'";
                    break;
                }
                case '&LT;': {
                    $sql .= "< '" . $option[2] . "'";
                    break;
                }
                case '&LT;=': {
                    $sql .= "<= '" . $option[2] . "'";
                    break;
                }
                case '&GT;': {
                    $sql .= "> '" . $option[2] . "'";
                    break;
                }
                case '&GT;=': {
                    $sql .= ">= '" . $option[2] . "'";
                    break;
                }
            }
        }

        $q = $this->pdo->prepare($sqlCount . $sql);
        $q->execute();
        $result = $q->fetch();

        $queryNumberResult = intval($result[0]);
        $maxPage = floor($queryNumberResult / $numitem) + 1;
        if ($currentPage > $maxPage) throw new Exception("Invalid Current Page");

        if ($orderColumn != NULL) {
            $sql .= " ORDER BY" . " " . $orderColumn;
            if ($orderType != NULL)
                $sql .= " " . $orderType;
        }
        if ($numitem != NULL) {
            if ($currentPage == 1)
                $lowerBound = ($currentPage - 1);
            else
                $lowerBound = ($currentPage - 1) * $numitem;
            $upperBound = $numitem;
            $sql .= " LIMIT" . " " . $lowerBound . "," . $upperBound;
        }
        $q = $this->pdo->prepare($sqlMaster . $sql);

        $q->execute();
        $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);

        $this->fill($queryResults);

        return $queryNumberResult;
    }

}

?>