<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Returns data Region
 *
 * @author ticarpa
 */
define("RESTDAO_DEF_LANGUAGE", "en_US");
class RestRegion extends Region {
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

    /**
     * Gets all regions
     *
     * @access public
     * @since unknown
     * @return array
     */
    public function findAllByCountryIsEmpty()
    {
        $this->dao->select('*');
        $this->dao->from($this->getTableName());
        $this->dao->orderBy('s_name', 'ASC');
        $result = $this->dao->get();

        if($result == false) {
            return array();
        }
        return $result->result();
    }
}
