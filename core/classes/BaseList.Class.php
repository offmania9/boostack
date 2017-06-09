<?php
/**
 * Boostack: BaseList.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 3.1
 */

abstract class BaseList implements IteratorAggregate, JsonSerializable
{

    /**
     * @var
     */
    protected $items;
    /**
     * @var
     */
    protected $pdo;
    /**
     * @var
     */
    protected $baseClassName;
    /**
     * @var
     */
    protected $baseClassTablename;

    /** List items object class */
    const BASE_CLASS = "";

    /**
     *
     */
    const ORDER_ASC = "ASC";
    /**
     *
     */
    const ORDER_DESC = "DESC";

    /**
     *
     */
    protected function init()
    {
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
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return mixed
     */
    public function getItemsArray()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function size()
    {
        return count($this->items);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->items) == 0;
    }

    /**
     * @param $element
     */
    public function add($element)
    {
        $this->items[] = $element;
    }

    public function haskey($key)
    {
        return array_key_exists($key,$this->items);
    }

    public function remove($key, $shift = true)
    {
        if(!$this->haskey($key))
            return false;
        unset($this->items[$key]);
        if($shift)
            $this->items = array_values($this->items);
        return true;
    }

    public function get($key)
    {
        return $this->items[$key];
    }

    public function clear()
    {
        $this->items = [];
    }

    /**
     * This method is used when json_encode() is called
     * It expose "items" to the json_encode() function
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * Retrieve values with field filtering, ordering and pagination
     */
    public function view($fields = NULL, $orderColumn = NULL, $orderType = NULL, $numitem = 25, $currentPage = 1)
    {
        try {
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
            $separator = " AND ";
            $count = 0;
            if(count($fields)>0){
                foreach ($fields as $option) {
                    if($count > 0) $sql .= $separator;
                    if ($option[0] == "datetime") {
                        $sql .= "FROM_UNIXTIME(" . $option[0] . ") ";
                    } else
                        $sql .= $option[0] . " ";
                    $option[1] = strtoupper($option[1]);
                    switch ($option[1]) {
                        case '<>':
                        case '&LT;&GT;': {
                            $sql .= "!= '" . $option[2] . "'";
                            break;
                        }
                        case '=': {
                            $sql .= $option[1] . " '" . $option[2] . "'";
                            break;
                        }
                        case '<':
                        case '&LT;': {
                            $sql .= "< '" . $option[2] . "'";
                            break;
                        }
                        case '<=':
                        case '&LT;=': {
                            $sql .= "<= '" . $option[2] . "'";
                            break;
                        }
                        case '>':
                        case '&GT;': {
                            $sql .= "> '" . $option[2] . "'";
                            break;
                        }
                        case '>=':
                        case '&GT;=': {
                            $sql .= ">= '" . $option[2] . "'";
                            break;
                        }
                        case 'LIKE': {
                            $sql .= $option[1] . " '%" . $option[2] . "%'";
                            break;
                        }
                        case 'NOT LIKE': {
                            $sql .= $option[1] . " '%" . $option[2] . "%'";
                            break;
                        }
                    }
                    $count++;
                }
            }

            $q = $this->pdo->prepare($sqlCount . $sql);
            $q->execute();
            $result = $q->fetch();

            $queryNumberResult = intval($result[0]);
            $maxPage = floor($queryNumberResult / $numitem) + 1;
            if ($currentPage > $maxPage){
                throw new Exception("Current page exceed max page");
            }

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
        } catch (PDOException $pdoEx) {
            Logger::write($pdoEx,Log_Level::ERROR, Log_Driver::FILE);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    /**
     * Fill the list with an array of array of object fields
     * For example with query results
     * ex. $array = [ 0 => [ "field1" => "value1", .. ], 1 => [..] ]
     */
    protected function fill($array)
    {
        foreach ($array as $elem) {
            $baseClassInstance = new $this->baseClassName;
            $baseClassInstance->fill($elem);
            $this->items[] = $baseClassInstance;
        }
    }

    /**
     * @return int
     */
    public function loadAll()
    {
        try {
            $sql = "SELECT * FROM " . $this->baseClassTablename;
            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            $this->fill($queryResults);
            $countResult = count($queryResults);
            return $countResult;
        } catch (PDOException $pdoEx) {
            Logger::write($pdoEx,Log_Level::ERROR, Log_Driver::FILE);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    public function truncate()
    {
        try {
            $sql = "TRUNCATE ".$this->baseClassTablename;
            $q = $this->pdo->prepare($sql);
            $q->execute();
            $this->items = array();
        } catch (PDOException $pdoEx) {
            Logger::write($pdoEx,Log_Level::ERROR, Log_Driver::FILE);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

}
?>