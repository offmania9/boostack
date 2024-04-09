<?php
namespace My\Models\Rest;
/**
 * Boostack: Rest_Api.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class Rest_CustomApi extends \Core\Models\Rest\Rest_Api
{
    /**
     * @return array|string
     */
    protected function getTest()
    {
        $this->constraints("GET");
        $res = (object) array(
            'message' => 'TEST API:OK'
        );
        return $res;
    }
}
