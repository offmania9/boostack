<?php
/**
 * Boostack: User_Social.Class.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
class User_Social extends User
{

    private $id;

    private $type;

    private $uid;

    private $uid_token;

    private $uid_token_secret;

    private $autosharing;

    private $website;

    private $extra;

    private $excluse_from_update = array();

    const TABLENAME = "boostack_user_social";

    public function __construct($id_u = -1, $type = "")
    {
        if ($id_u !== - 1 && $type !== "") {
            $sql = "SELECT * FROM " . self::TABLENAME . " WHERE id ='" . $id_u . "' AND type='" . $type . "' ";
            $fields = $this->pdo->query($sql)->fetchAll();
            $this->id = $fields["id"];
            $this->type = $fields["type"];
            $this->uid = $fields["uid"];
            $this->uid_token = $fields["uid_token"];
            $this->uid_token_secret = $fields["uid_token_secret"];
            $this->autosharing = $fields["autosharing"];
            $this->website = $fields["website"];
            $this->extra = $fields["extra"];
        }
    }

    public function prepare($post_array)
    {
        $fields["type"] = $post_array["type"];
        $fields["uid"] = $post_array["uid"];
        $fields["uid_token"] = $post_array["uid_token"];
        $fields["uid_token_secret"] = (isset($post_array["uid_token_secret"])) ? $post_array["uid_token_secret"] : "";
        $fields["autosharing"] = $post_array["autosharing"];
        $fields["website"] = $post_array["website"];
        $fields["extra"] = (isset($post_array["extra"])) ? $post_array["extra"] : "";
        
        foreach ($fields as $key => $value) {
            $this->$key = $value; // OBJECT UPDATE
        }
        return $fields;
    }

    public function insert($post_array, $id_u)
    {
        $fields = self::prepare($post_array);
        $sql_1 = "INSERT INTO " . self::TABLENAME . " (id";
        $sql_2 = "VALUES('" . $id_u . "'";
        foreach ($fields as $key => $value) {
            if ($key == "id")
                continue;
            $sql_1 .= ",$key";
            $sql_2 .= ",'$value'";
            // $this->$key = $value; #OBJECT UPDATE
        }
        $sql_1 .= ") ";
        $sql_2 .= ")";
        $sql = $sql_1 . $sql_2;
        $this->pdo->query($sql);
        $this->id = $this->pdo->lastInsertId();
        return true;
    }

    public function update($post_array, $excluse = NULL)
    {
        $fields = self::prepare($post_array);
        $sql = "UPDATE " . self::TABLENAME . " SET ";
        foreach ($fields as $key => $value) {
            if (in_array($key, $this->excluse_from_update) || in_array($key, $excluse))
                continue;
            $sql .= "$key='" . $value . "',";
            // $this->$key = $value; #OBJECT UPDATE
        }
        $sql = substr($sql, 0, - 1);
        $sql .= " WHERE id='" . $this->id . "'";
        $this->pdo->query($sql);
        return true;
    }

    public function delete()
    {
        $sql = "DELETE FROM " . self::TABLENAME . " WHERE id='" . $this->id . "'";
        $resurce = $this->pdo->query($sql);
        if ($resurce->rowCount() == 0)
            return false;
        return true;
    }

    public function __get($property_name)
    {
        if (isset($this->$property_name)) {
            return ($this->$property_name);
        } else {
            return (parent::__get($property_name));
        }
    }

    public function __set($property_name, $val)
    {
        if (isset($this->$property_name)) {
            $this->$property_name = $val;
            $sql = "UPDATE " . self::TABLENAME . " SET $property_name='" . $val . "'  WHERE id ='" . $this->id . "' ";
            $this->pdo->query($sql);
        } else
            parent::__set($property_name, $val);
    }

    public function isSynchronized($type)
    {
        $sql = "SELECT id FROM `" . self::TABLENAME . "` 
		WHERE id ='" . $this->id . "' AND type ='" . $type . "'";
        $q = $this->pdo->query($sql);
        return ($q->rowCount() > 0) ? true : false;
    }
    /*
     * public function getIdByUid($uid){
     * $sql = "SELECT id FROM ".self::TABLENAME." WHERE uid ='".$uid."' AND type='fb' ";
     * $q = $this->pdo->query($sql)->fetch();
     * return ($q->rowCount()>9 == 0)?NULL:$q[0];
     * }
     */
}
?>