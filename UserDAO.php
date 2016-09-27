<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RestDAO
 *
 * @author ticarpa
 */
define("RESTDAO_DEF_LANGUAGE", "en_US");

class RestUser extends User {
    private static $instance;

    public static function newInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Set data related to t_user table
     */
    function __construct()
    {
        parent::__construct();
    }

    public function findUserByPrimaryKey($userId = NULL)
    {
        $this->dao->select("pk_i_id, s_name, s_email, s_phone_mobile, s_city_area, md5(s_email) as email_gravatar");
        $this->setPrimaryKey('pk_i_id');
        $this->dao->from(DB_TABLE_PREFIX . 't_user');
        $this->dao->where('pk_i_id', $userId);
        $rs = $this->dao->get();
        if ($rs === false) {
            return array();
        }
        return $rs->result();
    }
}
