<?php
/**
 * Boostack: Cache.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class Cache
{
    const TABLENAME = "boostack_cache";
    const ALGO = "sha1";

    public static function has($key)
    {
        if(!Config::get("cache_enabled")) return false;
        $result = self::get($key);
        return $result != false;
    }

    public static function get($key)
    {
        if(!Config::get("cache_enabled")) return false;
        $hashedKey = self::hashKey($key);
        $pdo = Database_PDO::getInstance();
        $sql = "SELECT * FROM " . static::TABLENAME . " WHERE `key` = :key";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":key", $hashedKey);
        try {
            $stmt->execute();
        } catch (Exception $e) {
            Logger::write($e, Log_Level::WARNING, Log_Driver::DATABASE);
        }
        if (!$stmt->execute() || $stmt->rowCount() != 1) {
            return false;
        }
        $results = $stmt->fetch(PDO::FETCH_OBJ);
        return json_decode($results->value, true);
    }

    public static function set($key, $value)
    {
        if(!Config::get("cache_enabled")) return false;
        $hashedKey = self::hashKey($key);
        $pdo = Database_PDO::getInstance();
        $sql = "INSERT INTO " . static::TABLENAME . " (`key`, `value`, `created_at`) VALUES (:key, :value, :createdat)";
        $q = $pdo->prepare($sql);
        $q->bindValue(':key', $hashedKey);
        $q->bindValue(':value', json_encode($value));
        $q->bindValue(':createdat', time());
        try {
            $q->execute();
        } catch (Exception $e) {
            Logger::write($e, Log_Level::WARNING, Log_Driver::DATABASE);
        }
        return true;
    }

    public static function update($key, $value) {
        if(!Config::get("cache_enabled")) return false;
        if(!Cache::has($key)) return Cache::set($key, $value);
        $hashedKey = self::hashKey($key);
        $pdo = Database_PDO::getInstance();
        $sql = "UPDATE " . static::TABLENAME . " SET `value` = :value, `created_at` = :createdat WHERE `key` = :key";
        $q = $pdo->prepare($sql);
        $q->bindValue(':key', $hashedKey);
        $q->bindValue(':value', json_encode($value));
        $q->bindValue(':createdat', time());
        try {
            $q->execute();
        } catch (Exception $e) {
            Logger::write($sql, Log_Level::WARNING, Log_Driver::DATABASE);
            Logger::write($e, Log_Level::WARNING, Log_Driver::DATABASE);
        }
        return true;
    }

    private static function hashKey($key)
    {
        return hash(self::ALGO, $key);
    }

}