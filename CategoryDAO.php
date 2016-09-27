<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Rest Category retuns rest data categories
 *
 * @author ticarpa
 */
define("RESTDAO_DEF_LANGUAGE", "en_US");

class RestCategory extends Category {

    private static $instance;

    public static function newInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Set data related to t_category table
     */
    function __construct() {
        parent::__construct(RESTDAO_DEF_LANGUAGE);
    }
}
