<?php

/**
 * Boostack: Rest_ApiRequestList.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
class Rest_ApiRequestList extends BaseList
{

    const BASE_CLASS = Rest_ApiRequest::class;

    /**
     * Constructor method for initializing the object.
     */
    public function __construct()
    {
        parent::init();
    }

    /**
     * Retrieves statistics grouped by time slots (hours) within the last 24 hours.
     *
     * @return array Query results containing the count of requests grouped by timeslot and error status.
     * @throws PDOException Thrown if a database exception occurs. Check log file for details.
     */
    public function loadAllGroupByTimeSlot()
    {
        try {
            $sql = "SELECT COUNT(*) AS request_number, 
                        HOUR(created_datetime) as timeslot,
                        error 
                        FROM " . $this->baseClassTablename . " 
                        WHERE created_datetime >= now() - INTERVAL 1 DAY
                        GROUP BY timeslot,error 
                        ORDER BY timeslot";

            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            return $queryResults;
        } catch (PDOException $pdoEx) {
            Logger::write($pdoEx, Log_Level::ERROR, Log_Driver::FILE);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    /**
     * Retrieves total statistics grouped by error status.
     *
     * @return array Query results containing the count of requests grouped by error status.
     * @throws PDOException Thrown if a database exception occurs. Check log file for details.
     */
    public function loadAllTotalStats()
    {
        try {
            $sql = "SELECT COUNT(*) AS request_number, 
                        error 
                        FROM " . $this->baseClassTablename . " 
                        GROUP BY error";
            #SELECT COUNT(*) AS request_number, error FROM boostack_api_request GROUP BY timeslot,error

            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            return $queryResults;
        } catch (PDOException $pdoEx) {
            Logger::write($pdoEx, Log_Level::ERROR, Log_Driver::FILE);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    /**
     * Retrieves statistics grouped by response code (HTTP status code).
     *
     * @return array Query results containing the count of requests grouped by response code.
     * @throws PDOException Thrown if a database exception occurs. Check log file for details.
     */
    public function loadAllByCode()
    {
        try {
            $sql = "SELECT COUNT(*) AS request_number, 
                        code 
                        FROM " . $this->baseClassTablename . " 
                        GROUP BY code 
                        ORDER BY request_number DESC LIMIT 0,4";
            #SELECT COUNT(*) AS request_number, code FROM boostack_api_request GROUP BY code ORDER BY request_number DESC LIMIT 0,4

            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            return $queryResults;
        } catch (PDOException $pdoEx) {
            Logger::write($pdoEx, Log_Level::ERROR, Log_Driver::FILE);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }
}
