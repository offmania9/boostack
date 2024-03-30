<?php

/**
 * Boostack: Cache.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5
 */
class Cache
{
    const TABLENAME = "boostack_cache";
    const ALGO = "sha1";

    /**
     * Checks if the cache contains the specified key.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists in the cache, false otherwise.
     */
    public static function has($key)
    {
        if (!Config::get("cache_enabled")) return false;
        $result = self::get($key);
        return $result != false;
    }

    /**
     * Retrieves the value associated with the specified key from the cache.
     *
     * @param string $key The key to retrieve the value for.
     * @return mixed|false The value associated with the key if found, or false if the key does not exist.
     */
    public static function get($key)
    {
        if (!Config::get("cache_enabled")) return false;
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

    /**
     * Sets a key-value pair in the cache.
     *
     * @param string $key The key to set.
     * @param mixed $value The value to associate with the key.
     * @return bool True if the operation was successful, false otherwise.
     */
    public static function set($key, $value)
    {
        if (!Config::get("cache_enabled")) return false;
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
    /**
     * Updates the value associated with the specified key in the cache.
     *
     * @param string $key The key to update.
     * @param mixed $value The new value to associate with the key.
     * @return bool True if the update operation was successful, false otherwise.
     */
    public static function update($key, $value)
    {
        if (!Config::get("cache_enabled")) return false;
        if (!Cache::has($key)) return Cache::set($key, $value);
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

    /**
     * Generates a hashed representation of the cache key using the specified algorithm.
     *
     * @param string $key The key to hash.
     * @return string The hashed representation of the key.
     */
    private static function hashKey($key)
    {
        return hash(self::ALGO, $key);
    }
}
