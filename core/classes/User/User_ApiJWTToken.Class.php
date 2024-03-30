<?php

/**
 * Boostack: User_ApiJWTToken.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5
 */

require ROOTPATH . '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\Key;


class User_ApiJWTToken extends BaseClassTraced
{
    protected $id_user;
    protected $token;
    protected $issuer_url;
    protected $audience_url;
    protected $issued_time;
    protected $not_before_time;
    protected $expired_time;
    protected $expired_timestamp;
    protected $revoked_time;
    protected $revoked_from;

    const TABLENAME = "boostack_user_api";

    /**
     * @var array
     */
    protected $default_values = [
        "id_user" => 0,
        "token" => "",
        "issuer_url" => "",
        "audience_url" => "",
        "issued_time" => "",
        "not_before_time" => "",
        "expired_time" => "",
        "expired_timestamp" => "",
        "revoked_time" => NULL,
        "revoked_from" => NULL
    ];

    /**
     * Constructor.
     *
     * @param mixed|null $id The ID of the user.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->soft_delete = true;
    }

    /**
     * Decodes the JWT token and returns user data.
     *
     * @param string $received_token The JWT token to decode.
     * @return array The decoded user data.
     */
    public static function decode($received_token): array
    {
        $secret_key = Config::get("api_secret_key");
        $decoded_token = JWT::decode($received_token, new Key($secret_key, 'HS256'));
        $user_data = (array) $decoded_token;
        return $user_data;
    }

    /**
     * Checks if a JWT token is bound to a specific user.
     *
     * @param int $id_user The ID of the user.
     * @param string $received_token The JWT token to check.
     * @return User_ApiJWTToken|null The bound token object or null if not found.
     */
    public static function binded(int $id_user, $received_token): ?User_ApiJWTToken
    {
        $filter = array();
        $filter[] = array("id_user", "=", $id_user);
        $filter[] = array("token", "=", $received_token);
        $list = new User_ApiJWTTokenList();
        $total_items = $list->view($filter);
        if ($total_items == 1) {
            return $list->getItemsArray()[0];
        }
        return null;
    }

    /**
     * Checks the validity of the request for a JWT token.
     *
     * @return User The authenticated user.
     * @throws Exception If the user does not exist, is inactive, or the token is invalid.
     */
    public static function checkValidityRequestForJWTToken(): User
    {
        $jwt = str_replace('Bearer ', '', Request::getHeaderParam("Authorization"));
        $token = User_ApiJWTToken::decode($jwt);
        if (empty($token["data"]->id_user) || !User::existById($token["data"]->id_user))
            throw new Exception("User doesn't exist.");

        $user = new User($token["data"]->id_user);

        if ($user->active == "0")
            throw new Exception("User is not active");

        $bindedTokenObject = User_ApiJWTToken::binded($user->id, $jwt);
        if (empty($bindedTokenObject))
            throw new Exception("The token user does not match the user in the token payload");

        if ($bindedTokenObject->IsRevoked())
            throw new Exception("The token has been revoked");

        return $user;
    }

    /**
     * Revokes the JWT token.
     *
     * @param int|null $timestamp_from_revoke The timestamp from which to revoke the token.
     * @return bool True if the token was successfully revoked, false otherwise.
     */
    public function revoke(int $timestamp_from_revoke = null)
    {
        if (empty($this->revoked_time)) {
            $this->revoked_time = date('Y-m-d H:i:s', time());

            if (!empty($timestamp_from_revoke) && $timestamp_from_revoke < $this->expired_time && $timestamp_from_revoke > $this->issued_time)
                $rev_time = date('Y-m-d H:i:s', $timestamp_from_revoke);
            else
                $rev_time = $this->revoked_time;

            $this->revoked_from = $rev_time;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Checks if the JWT token is revoked.
     *
     * @return bool True if the token is revoked, false otherwise.
     */
    public function IsRevoked()
    {
        return !empty($this->revoked_time);
    }

    /**
     * Checks if the JWT token is currently revoked.
     *
     * @return bool True if the token is currently revoked, false otherwise.
     */
    public function IsNowRevoked()
    {
        return !empty($this->revoked_time) && $this->revoked_from <= date('Y-m-d H:i:s', time());
    }
}
