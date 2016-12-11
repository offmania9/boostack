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

    protected function exist($key) {
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

    public function loadAll() {
        global $boostack;
        $countResult = 0;
        $sql = "SELECT * FROM " . static::TABLENAME;
        try
        {
            $q = $this->pdo->prepare($sql);

            $q->execute();
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $logData)
            {
                $log = new Log();
                $log->fill($logData);
                $this->items[] = $log;
            }
            $countResult = count($result);
        }
        catch (Exception $e)
        {
            $boostack->writeLog('LogList -> loadAll -> Caught exception: '.$e->getMessage());
        }
        return $countResult;
    }


    public function view($fieldViewArray=NULL, $fieldOrder=NULL,$fieldOrderType=NULL, $numitem=25, $currentPage=1)
    {
        global $boostack;
        $sql = "";
        $queryNumberResult = 0;

        try
        {
            $orderType = strtoupper($fieldOrderType);
            //current page deve essere cmq un numero probabile, compreso tra 1 e mexpage
            if(is_numeric($numitem) && is_numeric($currentPage) && $currentPage > 0 && ($orderType == "DESC" || $orderType == "ASC")) {
                $sqlCount = "SELECT count(id) FROM " . static::TABLENAME . " ";
                $sqlMaster = "SELECT * FROM " . static::TABLENAME . " ";

                if (is_array($fieldViewArray) && count($fieldViewArray) > 0) {
                    $sqlParams = array();
                    $sql .= "WHERE" . " ";
                    foreach ($fieldViewArray as $option) {
                        if ($option[0] == "datetime") {
                            $sql .= "FROM_UNIXTIME(" . $option[0] . ") ";
                        } else
                            $sql .= $option[0] . " ";
                        $option[1] = strtoupper($option[1]);
                        switch ($option[1]) {
                            case '&LT;&GT;': {
                                $sql .=   "!= '" . $option[2] . "'";
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
                }

                $q = $this->pdo->prepare($sqlCount . $sql);
                $q->execute();
                $result = $q->fetch();

                $queryNumberResult = intval($result[0]);
                $maxPage = floor($queryNumberResult/$numitem)+1;
                if($currentPage>$maxPage)
                    throw new Exception("Invalid Current Page");


                if ($fieldOrder != NULL) {
                    $sql .= " ORDER BY" . " " . $fieldOrder;
                    if ($fieldOrderType != NULL)
                        $sql .= " " . $fieldOrderType;
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
                //return $option[1];
                $q->execute();
                $result = $q->fetchAll(PDO::FETCH_ASSOC);

                foreach ($result as $logList) {
                    $log = new Log();
                    $log->fill($logList);
                    $this->items[] = $log;
                }
            }
            else
                $boostack->writeLog('BaseList -> view -> wrong input type',"error");
            //throw new Exception("wrong type");
        }
        catch (PDOException $e)
        {
            $boostack->writeLog('BaseList -> view -> Caught PDOException: '.$e->getMessage(),"error");
            $queryNumberResult = 0;
        }
        catch ( Exception $b )
        {
            $boostack->writeLog('BaseList -> view -> Caught Exception: '.$b->getMessage(),"error");
            $queryNumberResult = 0;
        }

        return $queryNumberResult;
    }

}

?>