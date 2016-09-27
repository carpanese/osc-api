<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * RestItem return data Items.
 *
 * @author ticarpa
 */
define("RESTDAO_DEF_LANGUAGE", "en_US");

class RestItem extends Item {

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
     * Find images by item
     * @param null $itemID
     * @return array
     */
    public function findImageItemByPrimaryKey($itemID = NULL) {
        $this->dao->select("*");
        $this->dao->from(DB_TABLE_PREFIX . 't_item_resource');
        $this->dao->where('fk_i_item_id', $itemID);
        $rs = $this->dao->get();
        if ($rs === false) {
            return array();
        }
        return $rs->result();
    }

    /**
     * Get the result match of the primary key passed by parameter, extended with
     * location information and number of views.
     *
     * @access public
     * @since unknown
     * @param int $id Item id
     * @return array
     */
    public function findItemByPrimaryKey($id)
    {
        if( !is_numeric($id) || $id == null ) {
            return array();
        }
        $this->dao->select('l.*, i.*, md5(i.s_contact_email) as email_gravatar,SUM(s.i_num_views) AS i_num_views');
        $this->dao->from($this->getTableName().' i');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_location l', 'l.fk_i_item_id = i.pk_i_id ', 'LEFT');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_stats s', 'i.pk_i_id = s.fk_i_item_id', 'LEFT');
        $this->dao->where('i.pk_i_id', $id);
        $this->dao->groupBy('s.fk_i_item_id');
        $result = $this->dao->get();

        if($result === false) {
            return false;
        }

        if( $result->numRows() == 0 ) {
            return array();
        }

        $item = $result->row();

        if(!is_null($item) ) {
            return $this->extendDataSingle($item);
        } else {
            return array();
        }
    }

    /**
     * List items ordered by pub date
     *
     * @access public
     * @since unknown
     * @return array of items
     */
    public function latestAddItems()
    {
        $this->dao->select( 'i.*, res.s_extension as extension, res.pk_i_id as id_image, res.s_path as path' );
        $this->dao->from($this->getTableName().' i' );
        $this->dao->join(DB_TABLE_PREFIX.'t_item_resource res', 'i.pk_i_id = res.fk_i_item_id ', 'INNER');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_description d', 'i.pk_i_id = d.fk_i_item_id ', 'INNER');
        $this->dao->where("res.fk_i_item_id = d.fk_i_item_id");
        $this->dao->where(" i.b_active = 1 AND i.b_enabled = 1 ");
        $this->dao->groupBy('i.pk_i_id ');
        $this->dao->orderBy('i.dt_pub_date', 'DESC');
        $this->dao->limit(5);
        $result = $this->dao->get();
        if($result == false) {
            return array();
        }
        $items  = $result->result();


        return $this->extendData($items);
    }

    /**
     * List items ordered by views
     *
     * @access public
     * @since unknown
     * @return array of items
     */
    public function premiumItems()
    {
        $this->dao->select( 'i.*, res.s_extension as extension, res.pk_i_id as id_image, res.s_path as path' );
        $this->dao->from($this->getTableName().' i' );
        $this->dao->join(DB_TABLE_PREFIX.'t_item_resource res', 'i.pk_i_id = res.fk_i_item_id ', 'INNER');
        $this->dao->join(DB_TABLE_PREFIX.'t_item_description d', 'i.pk_i_id = d.fk_i_item_id ', 'INNER');
        $this->dao->where("res.fk_i_item_id = d.fk_i_item_id ");
        $this->dao->where(" i.b_active = 1 AND i.b_enabled = 1 AND i.b_premium = 1");
        $this->dao->groupBy('i.pk_i_id ');
        $this->dao->orderBy('i.dt_pub_date', 'DESC');
        $this->dao->limit(5);

        $result = $this->dao->get();
        if($result == false) {
            return array();
        }
        $items  = $result->result();

        return $this->extendData($items);
    }
}
