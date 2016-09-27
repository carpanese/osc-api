<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Returns data from Google maps app
 *
 * @author ticarpa
 */
define("RESTDAO_DEF_LANGUAGE", "en_US");

class RestMap extends Item {

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
        parent::__construct();
    }

    /**
     * Find items by Latitude and Longitude and distance
     * @param null $lat
     * @param null $long
     * @param null $distance
     * @return array
     */
    public function findItemsMapByLatLongCity($lat = NULL, $long = NULL, $distance = NULL) {

        $this->dao->select("l.fk_i_item_id as id, l.d_coord_lat as latitude, l.d_coord_long as longitude, d.s_title as title, res.s_path as path, res.pk_i_id as id_image, res.s_extension as extension ");
        $this->dao->select(" ( 6371 * acos( cos( radians('$lat') ) * cos( radians( `d_coord_lat` ) ) * cos( radians( `d_coord_long` ) - radians('$long') ) + sin( radians('$lat') ) * sin( radians( `d_coord_lat` ) ) ) ) AS distance ");
        $this->dao->from(DB_TABLE_PREFIX . 't_item_location as l');
        $this->dao->join(DB_TABLE_PREFIX.'t_item as i','i.pk_i_id = l.fk_i_item_id','INNER');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_description as d','i.pk_i_id = d.fk_i_item_id','INNER');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_resource as res','i.pk_i_id = res.fk_i_item_id','INNER');

        //Kilometers ditance center point
        $this->dao->having('distance <', $distance);
        $this->dao->groupBy(" res.fk_i_item_id ");
        $this->dao->limit(50);
        $this->dao->orderBy('distance', 'ASC');
        $rs = $this->dao->get();

        if ($rs === false) {
            return array();
        }
        return $rs->result();
    }

    /**
     * Find position by cityID
     * @param null $cityId
     * @return array
     */
    public function findOnePositionByCity($cityId = NULL) {

        $this->dao->select("l.fk_i_city_id, l.s_city, l.fk_i_item_id as id, l.d_coord_lat as latitude, l.d_coord_long as longitude, d.s_title as title, res.s_path as path, res.pk_i_id as id_image, res.s_extension as extension ");
        $this->dao->from(DB_TABLE_PREFIX . 't_item_location as l');
        $this->dao->join(DB_TABLE_PREFIX.'t_item as i','i.pk_i_id = l.fk_i_item_id','INNER');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_description as d','i.pk_i_id = d.fk_i_item_id','INNER');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_resource as res','i.pk_i_id = res.fk_i_item_id','INNER');

        $this->dao->where("l.fk_i_city_id", $cityId);
        $this->dao->groupBy(" res.fk_i_item_id ");
        $this->dao->limit(1);
        $rs = $this->dao->get();

        if ($rs === false) {
            return array();
        }
        return $rs->result();
    }
}
