<?php
namespace My\Models\Rest;
use Core\Models\Auth;
use Core\Models\User\User_ApiJWTToken;
/**
 * Boostack: Rest_UserApi.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class Rest_ApiUserAuth extends \Core\Models\Rest\Rest_Api
{
    /**
     * Handle an authentication request using a JWT token for a user.
     * 
     * @return MessageBag
     * Method: POST 
     * 
     * curl [URL]/api/authenticate \
     * -H "Content-Type: application/json" \
     * -H "Authorization: Bearer $JWT_TOKEN" \
     * -d '{}'
     */
    protected function authenticate()
    {
        $this->constraints(
            "POST",
            false,
            array(
                "Content-Type" => "application/json",
                "Authorization" => "*"
            )
        );
        $res = array();
        $user = User_ApiJWTToken::getUserFromJWTToken();

        //---- Custom logic
        Auth::loginByUserID($user->id);
        $res = array(
            "id_user" => $user->id,
            "username" => $user->username,
            "email" => $user->email,
            "action" => "User is correctly logged in",
        );
        //---- End Custom logic
        return $res;
    }
}
