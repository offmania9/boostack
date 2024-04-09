<?php
namespace Core\Models\User;
use Core\Models\Auth;
/**
 * Boostack: User_ApiJWTTokenList.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class User_ApiJWTTokenList extends \Core\Models\BaseList
{
    const BASE_CLASS = User_ApiJWTToken::class;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::init();
    }

    /**
     * Retrieves all items associated with the currently logged-in user.
     *
     * @return array An array containing items associated with the user and the total count.
     */
    public function getMy()
    {
        parent::clear();
        return self::getByUser(Auth::getUserLoggedObject()->id);
    }

    /**
     * Retrieves items associated with a specific user.
     *
     * @param int $id_user The ID of the user.
     * @return array An array containing items associated with the user and the total count.
     */
    public function getByUser(int $id_user)
    {
        $filter = array();
        $filter[] = array("id_user", "=", $id_user);
        $ti = $this->view($filter, "last_update", "desc");
        return array("items" => $this->getItemsArray(), "total_items" => $ti);
    }

    /**
     * Revokes all items associated with the currently logged-in user.
     *
     * @param int|null $timestamp_from_revoke The timestamp from which to revoke the items.
     */
    public function revokeAll(int $timestamp_from_revoke = null)
    {
        if (count($this->items) > 0) {
            foreach ($this->items as $item) {
                $item->revoke($timestamp_from_revoke);
            }
        }
    }
}
