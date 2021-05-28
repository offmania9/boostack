<?php

class Rest_ApiRequestList extends BaseList {

    const BASE_CLASS = Rest_ApiRequest::class;

    public function __construct() {
        parent::init();
    }

    public function loadAllGroupByTimeSlot() {
        try {
            $sql = "SELECT COUNT(*) AS request_number, 
                            HOUR(created_datetime) as timeslot,
                            error 
                            FROM " . $this->baseClassTablename. " 
                            WHERE created_datetime >= now() - INTERVAL 1 DAY
                            GROUP BY timeslot,error 
                            order by timeslot";
                            #SELECT COUNT(*) AS request_number, HOUR(created_datetime) as timeslot, error FROM boostack_api_request WHERE created_datetime >= now() - INTERVAL 1 DAY GROUP BY timeslot,error order by timeslot

            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            return $queryResults;
        } catch (PDOException $pdoEx) {
            FileLogger::getInstance()->log($pdoEx);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    public function loadAllTotalStats() {
        try {
            $sql = "SELECT COUNT(*) AS request_number, 
                            error 
                            FROM " . $this->baseClassTablename. " 
                            GROUP BY error";
                            #SELECT COUNT(*) AS request_number, error FROM boostack_api_request GROUP BY timeslot,error
                            
            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            return $queryResults;
        } catch (PDOException $pdoEx) {
            FileLogger::getInstance()->log($pdoEx);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

    public function loadAllByCode() {
        try {
            $sql = "SELECT COUNT(*) AS request_number, 
                            code 
                            FROM " . $this->baseClassTablename. " 
                            GROUP BY code 
                            ORDER BY request_number DESC LIMIT 0,4";
                            #SELECT COUNT(*) AS request_number, code FROM boostack_api_request GROUP BY code ORDER BY request_number DESC LIMIT 0,4
                            
            $q = $this->pdo->prepare($sql);
            $q->execute();
            $queryResults = $q->fetchAll(PDO::FETCH_ASSOC);
            return $queryResults;
        } catch (PDOException $pdoEx) {
            FileLogger::getInstance()->log($pdoEx);
            throw new PDOException("Database Exception. Please see log file.");
        }
    }

}