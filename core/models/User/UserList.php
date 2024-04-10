<?php
namespace Core\Models\User;
use Core\Models\Database\Database_PDO;
use Core\Models\Log\Log_Driver;
use Core\Models\Log\Log_Level;
use Core\Models\Log\Logger;
use Core\Models\User\User_Entity;
/**
 * Boostack: User_List.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */
class UserList extends \Core\Models\BaseList
{

    protected $PDO = null;

    protected $items = null;

    protected $objects = null;

    protected $baseClassName = User::class;

    protected $mainTablename = null;

    protected $otherTablenames = array();

    /**
     * Creates a new instance of the class, saving the database tables associated with the provided classes as parameters.
     */
    public function __construct($classes = array(User_Entity::class))
    {
        $this->PDO = Database_PDO::getInstance();
        $this->items = [];
        $this->objects = $classes;
        $classesCount = count($classes);
        $this->mainTablename = (new $this->objects[0])->getTablename();
        if ($classesCount > 1) {
            for ($j = 1; $j < $classesCount; $j++) {
                $this->otherTablenames[] = (new $this->objects[$j])->getTablename();
            }
        }
    }

    /**
     * Loads all elements present in the table.
     *
     * @param string|null $orderColumn The column to order the results by.
     * @param string|null $orderType The order type (ASC or DESC).
     * @return int The number of loaded elements.
     * @throws \PDOException If a database \Exception occurs.
     */
    public function loadAll($orderColumn = null, $orderType = null)
    {
        try {
            $ob = $orderColumn == null ? "" : " ORDER BY " . $orderColumn;
            $ot = $orderColumn == null ? "" : " " . $orderType;
            $sql = "SELECT * " . $this->getSQLFromJoinPart();
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
     * Fills the object with an array containing arrays of attributes for each instance, invoking the fill method of the individual object.
     * By default, the password (if present among the parameters) is excluded to prevent re-hashing.
     *
     * @param array $array The array containing data for filling the objects.
     * @param bool $excludePwd Flag to exclude password from being hashed.
     * @return void
     */
    protected function fill($array, $excludePwd = true)
    {
        foreach ($array as $elem) {
            $baseClassInstance = new $this->baseClassName;
            // Exclude the password to prevent re-hashing
            if ($excludePwd) unset($elem["pwd"]);
            $baseClassInstance->fill($elem);
            $this->items[] = $baseClassInstance;
        }
    }

    /**
     * Generates the query part containing the FROM clause, inserting JOIN clauses if multiple classes are included.
     *
     * @return string The generated SQL query part.
     */
    private function getSQLFromJoinPart()
    {
        $sql = " FROM " . $this->mainTablename;
        $otherTablenamesCount = count($this->otherTablenames);
        if ($otherTablenamesCount > 0) {
            foreach ($this->otherTablenames as $otherTable) {
                $sql .= " JOIN " . $otherTable . " ON " . $this->mainTablename . ".id = " . $otherTable . ".id";
            }
        }
        return $sql;
    }
}
