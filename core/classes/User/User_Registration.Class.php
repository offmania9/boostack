<?php

/**
 * Boostack: User_Registration.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class User_Registration extends BaseClass
{
    /**
     * @var
     */
    protected $activation_date;
    /**
     * @var
     */
    protected $access_code;
    /**
     * @var
     */
    protected $ip;
    /**
     * @var
     */
    protected $join_date;
    /**
     * @var
     */
    protected $join_idconfirm;

    /**
     *
     */
    const TABLENAME = "boostack_user_registration";

    /**
     * @var array
     */
    protected $default_values = [
        "activation_date" => 0,
        "access_code" => "",
        "ip" => "",
        "join_date" => 0,
        "join_idconfirm" => "",
    ];

    /**
     * User_Registration constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::init($id);
    }

    public static function getUserIDJoinIdConfirm($join_idconfirm,$throwException=true) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM ".self::TABLENAME." WHERE join_idconfirm = :join_idconfirm ";
        $q = $pdo->prepare($query);
        $q->bindParam(":join_idconfirm", $join_idconfirm);
        $q->execute();
        if ($q->rowCount() == 0) {
            if ($throwException)
                throw new Exception("Attention! confirm token not found.", 0);
            return false;
        }
        $res = $q->fetchAll(PDO::FETCH_ASSOC);
        return (int)$res[0]["id"];
    }
}
?>