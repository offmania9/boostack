<?php
namespace Core\Models;
use Core\Models\Database\Database_PDO;
use Core\Models\Log\Log_Driver;
use Core\Models\Log\Log_Level;
use Core\Models\Log\Logger;
/**
 * Boostack: BaseList.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
abstract class BaseList implements \IteratorAggregate, \JsonSerializable
{

    protected $items;

    protected $PDO;

    protected $baseClassName;

    protected $baseClassTablename;

    /** List items object class */
    const BASE_CLASS = "";

    const ORDER_ASC = "ASC";

    const ORDER_DESC = "DESC";


    protected function init()
    {
        $this->PDO = Database_PDO::getInstance();
        $this->items = [];
        $this->baseClassName = static::BASE_CLASS;
        $this->baseClassTablename = (new $this->baseClassName)->getTablename();
    }

    /**
     * Returns an iterator for iterating through the list like an array.
     * @return \ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Returns the items array.
     * @return mixed
     */
    public function getItemsArray()
    {
        return $this->items;
    }

    /**
     * Returns the size of the list.
     * @return int
     */
    public function size()
    {
        return count($this->items);
    }

    /**
     * Checks if the list is empty.
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->items) == 0;
    }

    /**
     * Adds an element to the list.
     * @param $element
     */
    public function add($element)
    {
        $this->items[] = $element;
    }
    /**
     * Clears the items array.
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * Converts the list items to an array.
     * @return mixed
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Exposes the items to the json_encode() function.
     */
    public function jsonSerialize(): mixed
    {
        return $this->items;
    }

    /**
     * Retrieves values with field filtering, ordering, and pagination.
     * @param array|null $fields
     * @param string $orderColumn
     * @param string $orderType
     * @param int $numitem
     * @param int $currentPage
     * @return int
     * @throws \Exception
     */
    public function view(array $fields = null, $orderColumn = "", $orderType = "ASC", $numitem = 25, $currentPage = 1): int
    {
        try {
            $sql = "";
            $orderType = strtoupper($orderType);

            $error = false;
            if (!is_numeric($numitem)) $error = "Wrong num_item type";
            if (!is_numeric($currentPage) || $currentPage < 0) $error = "Wrong current_page format";
            if (!($orderType == self::ORDER_ASC || $orderType == self::ORDER_DESC)) $error = "Wrong order_type format";
            if (!(is_array($fields) && count($fields) > 0)) $error = "Wrong field_view format";
            if ($error !== false) throw new \Exception($error);

            $sqlCount = "SELECT count(id) FROM " . $this->baseClassTablename . " ";
            $sqlMaster = "SELECT * FROM " . $this->baseClassTablename . " ";

            $sql .= "WHERE" . " ";
            $separator = " AND ";
            $count = 0;
            if (count($fields) > 0) {
                foreach ($fields as $option) {
                    if ($count > 0) $sql .= $separator;
                    if ($option[0] == "datetime") {
                        $sql .= $option[0] . " ";
                    } else
                        $sql .= $option[0] . " ";
                    $option[1] = strtoupper($option[1]);
                    switch ($option[1]) {
                        case '<>':
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
                    }
                    $count++;
                }
            }
            $q = $this->PDO->prepare($sqlCount . $sql);
            $q->execute();
            $result = $q->fetch();

            $queryNumberResult = intval($result[0]);

            $maxPage = floor($queryNumberResult / $numitem) + 1;
            if ($currentPage > $maxPage) {
                $maxPage = floor($queryNumberResult / 25) + 1;
                $currentPage = 1;
            };

            if ($orderColumn != "") {
                $sql .= " ORDER BY" . " " . $orderColumn;
                if ($orderType != "")
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
            $q = $this->PDO->prepare($sqlMaster . $sql);

            $q->execute();
            $queryResults = $q->fetchAll(\PDO::FETCH_ASSOC);

            $this->fill($queryResults);

            return $queryNumberResult;
        } catch (\PDOException $PDOEx) {
            Logger::write($PDOEx->getMessage(), Log_Level::ERROR, Log_Driver::FILE);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Checks if a key exists in the items array.
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Removes an item from the items array.
     * @param $key
     * @param bool $shift
     * @return bool
     */
    protected function remove($key, $shift = true)
    {
        if ($shift)
            array_splice($this->items, $key, 1);
        else
            unset($this->items[$key]);
        return true;
    }

    /**
     * Retrieves an item from the items array.
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->items[$key];
    }

    /**
     * Fills the list with an array of object fields.
     * For example, with query results.
     * @param array $array
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
     * Loads all items from the database.
     * @param string|null $orderColumn
     * @param string|null $orderType
     * @return int
     * @throws \PDOException
     */
    public function loadAll($orderColumn = NULL, $orderType = NULL)
    {
        try {
            $ob = $orderColumn == NULL ? "" : " ORDER BY " . $orderColumn;
            $ot = $orderType == NULL ? "" : " " . $orderType;
            $sql = "SELECT * FROM " . $this->baseClassTablename . $ob . $ot;
            $q = $this->PDO->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(\PDO::FETCH_ASSOC);
            $this->fill($queryResults);
            $countResult = count($queryResults);
            return $countResult;
        } catch (\PDOException $PDOEx) {
            Logger::write($PDOEx->getMessage(), Log_Level::ERROR, Log_Driver::FILE);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Retrieves the columns of the table.
     * @param bool $withoutTraced
     * @return mixed
     * @throws \PDOException
     */
    public function getColumns($withoutTraced = false)
    {
        try {
            if ($withoutTraced) {
                $sql = "SELECT column_name FROM information_schema.columns 
            WHERE table_name = '" . $this->baseClassTablename . "' AND table_schema='" . Config::get("db_name") . "' 
AND column_name NOT IN ('created_at', 'last_update','last_access','created_at_datetime')";
            } else {
                $sql = "DESCRIBE " . $this->baseClassTablename;
            }
            $q = $this->PDO->prepare($sql);
            $q->execute();
            return $q->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $PDOEx) {
            Logger::write($PDOEx->getMessage(), Log_Level::ERROR, Log_Driver::FILE);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }
}
