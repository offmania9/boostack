<?php
/**
 * Boostack: User_List.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class UserList extends BaseList {

    protected $pdo = null;
    protected $items = null;
    protected $objects = null;
    protected $baseClassName = User::class;

    protected $mainTablename = null;
    protected $otherTablenames = array();

    /**
     * Crea una nuovai stanza della classe, salvando le tabelle del database associate con le relative classi passate come parametro.
     */
    public function __construct($classes = array(User_Entity::class)) {
        $this->pdo = Database_PDO::getInstance();
        $this->items = [];
        $this->objects = $classes;
        $classesCount = count($classes);
        $this->mainTablename = (new $this->objects[0])->getTablename();
        if($classesCount > 1) {
            for($j = 1; $j < $classesCount; $j++) {
                $this->otherTablenames[] = (new $this->objects[$j])->getTablename();
            }
        }
    }

    /**
     * Esegue il load di tutti gli elementi presenti nella tabella.
     */
    public function loadAll() {
        try {
            $sql = "SELECT * ".$this->getSQLFromJoinPart();
            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            $this->fill($queryResults);
            $countResult = count($queryResults);
            return $countResult;
        } catch (PDOException $pdoEx) {
            FileLogger::getInstance()->log($pdoEx);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    /**
     * Esegue il load degli elementi che rispettano i filtri passati come parametro
     */
    public function view($fields = NULL, $orderColumn = NULL, $orderType = NULL, $numitem = 25, $currentPage = 1) {
        try {
            $sql = "";
            $orderType = strtoupper($orderType);

            $error = false;
            if (!is_numeric($numitem)) $error = "Wrong num_item type";
            if (!is_numeric($currentPage) || $currentPage < 0) $error = "Wrong current_page format";
            if (!($orderType == self::ORDER_ASC || $orderType == self::ORDER_DESC)) $error = "Wrong order_type format";
            if (!(is_array($fields) && count($fields) > 0)) $error = "Wrong field_view format";
            if ($error !== false) throw new Exception($error);

            $sqlFromJoinPart = $this->getSQLFromJoinPart();
            $sqlCount = "SELECT count(*) ".$sqlFromJoinPart;
            $sqlMaster = "SELECT * ".$sqlFromJoinPart;

            $sql .= " WHERE" . " ";
            $separator = " AND ";
            $count = 0;
            if(count($fields)>0){
                foreach ($fields as $option) {
                    if($count > 0) $sql .= $separator;
                    if ($option[0] == "datetime") {
                        $sql .= "FROM_UNIXTIME(" . $option[0] . ") ";
                    } else
                        if($option[0] == "id") $option[0] = $this->mainTablename.".id";
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


            $q = $this->pdo->prepare($sqlCount . $sql);
            $q->execute();
            $result = $q->fetch();

            $queryNumberResult = intval($result[0]);
            $maxPage = floor($queryNumberResult / $numitem) + 1;
            if ($currentPage >= $maxPage){
                $maxPage = floor($queryNumberResult / 25) + 1;
                $currentPage = 1;
            };

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
            FileLogger::getInstance()->log($pdoEx);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    /**
     * Riempie l'oggetto con un array contentente a sua volta un array di attributi per ogni istanza, richiamando la fill del singolo oggetto.
     * Di default, la password (se presente tra i parametri) viene esclusa in modo da non causare un nuovo re-hash.
     */
    protected function fill($array, $excludePwd = true) {
        foreach ($array as $elem) {
            $baseClassInstance = new $this->baseClassName;
            // escludo la password in modo da non hasharla ogni volta
            if($excludePwd) unset($elem["pwd"]);
            $baseClassInstance->fill($elem);
            $this->items[] = $baseClassInstance;
        }
    }

    /**
     * Genera la parte di query contenente il FROM inserendo eventuali JOIN nel caso siano incluse più classi.
     */
    private function getSQLFromJoinPart() {
        $sql = " FROM " . $this->mainTablename;
        $otherTablenamesCount = count($this->otherTablenames);
        if($otherTablenamesCount > 0) {
            foreach($this->otherTablenames as $otherTable) {
                $sql .= " JOIN ".$otherTable." ON ".$this->mainTablename.".id = ".$otherTable.".id";
            }
        }
        return $sql;
    }

}
?>