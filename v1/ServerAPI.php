<?php

/**
 * OSClass API Server
 */
class ServerAPI {


    /**
     * Return online api
     *
     * @url GET /check
     */
    public function serviceCheck(){
        return 200;
    }

    /**
     * Return all locales enabled.
     * 
     * @url GET /locale
     * @url GET /locale/$code
     */
    public function getLocale($code) {
        if ($code) {
            $result = OSCLocale::newInstance()->findByCode($code);
        } else {
            $result = OSCLocale::newInstance()->listAllEnabled();
        }
        return ($result);
    }

    /**
     * Return All Countries
     *
     * @url GET /country
     * @url GET /country/$code
     */
    public function getCountry($code) {
        if ($code) {
            return(CountryStats::newInstance()->findByCountryCode($code));
        }
        return(CountryStats::newInstance()->listCountries(">="));
    }

    /**
     * List Regions by Country Code.
     *
     * @url GET /region
     * @url GET /country/$code/region
     */
    public function getRegionbyCountry($code) {
        if ($code) {
            return( Region::newInstance()->findByCountry($code));
        }
        return(RestRegion::newInstance()->findAllByCountryIsEmpty());
    }

    /**
     * GET City and States by city Id
     * List city by region id
     * 
     * @url GET /cities/$regionid
     */
    public function getCityListbyRegion($regionid) {
        if ($regionid) {
            return(CityStats::newInstance()->listCities($regionid, ">=", $order = "city_name ASC"));
        }
        return array();
    }

    /**
     * Get Category all lang
     * list all or single by categoryid
     * 
     * @url GET /category
     * @url GET /category/$id
     */
    public function getCategory($id) {
        if($id){
           return(RestCategory::newInstance()->findSubcategories($id));
        }
        return(RestCategory::newInstance()->findRootCategories());
    }

    /**
     * GET Category
     * 
     * @url GET /categorylang/$lang
     */
    public function getCategorylang($lang) {
        if ($lang) {
            return(RestCategory::newInstance()->restListEnabled($lang));
        }
        return array();
    }

    /**
     * GET items by category
     * 
     * @url GET /category/$categoryid/items     * 
     */
    public function getItemsbyCategory($categoryid){
        return(Item::newInstance()->findByCategoryID($categoryid));
    }

    /**
     * GET Item by Id
     *
     * @url GET /item/$id
     */
    public function getItem($id) {
        if ($id) {
            return (RestItem::newInstance()->findItemByPrimaryKey($id));
        }
        return array();
    }


    /**
     * GET News Items
     *
     * @url GET /items/news
     */
    public function getNewsItem() {
        return (RestItem::newInstance()->latestAddItems());
    }

    /**
     * GET News Items
     *
     * @url GET /items/premium
     */
    public function getMostViewsItem() {
        return (RestItem::newInstance()->premiumItems());
    }



    /**
     * GET Item images by Id
     *
     * @url GET /item/$itemId/images
     */
    public function getItemImagesId($itemId) {
        if ($itemId) {
            return (RestItem::newInstance()->findImageItemByPrimaryKey($itemId));
        }
        return array();
    }

    /**
     * Get user by id
     *
     * @url GET /user/$id
     */
    public function getUser($id) {
        if($id){
            return(RestUser::newInstance()->findUserByPrimaryKey($id));
        }
        return(RestUser::newInstance()->restListEnableGlobal());
    }

    /**
     * Get User all lang
     * list all or single by userid
     *
     * @url POST /map
     */
    public function getItemsMap() {
        $json = file_get_contents('php://input');
        $obj = json_decode($json);
        $lat = $obj->latitude;
        $long = $obj->longitude;
        $distance = $obj->distance;
        return(RestMap::newInstance()->findItemsMapByLatLongCity($lat, $long, $distance));
    }

    /**
     * Get User all lang
     * list all or single by userid
     *
     * @url POST /map/position-city
     */
    public function getPositionByCity() {
        $json = file_get_contents('php://input');
        $obj = json_decode($json);
        $city = $obj->cityId;
        return(RestMap::newInstance()->findOnePositionByCity($city));
    }

    /**
     * Get Items Search
     * list all or single by userid
     *
     * @url POST /item/search
     */
    public function getSearchAll() {

        $json = file_get_contents('php://input');
        $filter = json_decode($json);
        $pattern = $filter->sPattern;
        $page = $filter->page;
        $regionId = $filter->regionId;
        $cityId = $filter->cityId;
        $categoryId = $filter->categoryId;
        $minPrice = $filter->minPrice;
        $maxPrice = $filter->maxPrice;
        $onlyPremium = $filter->onlyPremium;
        $onlyPhotos = $filter->onlyPhotos;

        $mSearch = new Search();

        if($pattern){
            $mSearch->addPattern($pattern);
            $mSearch->addJoinTable(0, DB_TABLE_PREFIX.'t_item_resource res', DB_TABLE_PREFIX.'t_item.pk_i_id = res.fk_i_item_id', 'INNER');
            $mSearch->addGroupBy(sprintf(" %st_item.pk_i_id ", DB_TABLE_PREFIX));
        } else {
            $mSearch->addJoinTable(0, DB_TABLE_PREFIX.'t_item_resource res', DB_TABLE_PREFIX.'t_item.pk_i_id = res.fk_i_item_id', 'INNER');
            $mSearch->addJoinTable(1, DB_TABLE_PREFIX.'t_item_description d', DB_TABLE_PREFIX.'t_item.pk_i_id = d.fk_i_item_id', 'INNER');
            $mSearch->addConditions("res.fk_i_item_id = d.fk_i_item_id");
            $mSearch->addGroupBy(sprintf(" %st_item.pk_i_id ", DB_TABLE_PREFIX));
        }

        if($regionId){
            $mSearch->addRegion($regionId);
        }

        if($cityId){
            $mSearch->addCity($cityId);
        }

        if($categoryId){
            $mSearch->addCategory($categoryId);
        }

        if($minPrice){
            $mSearch->priceMin($minPrice);
        }

        if($maxPrice){
            $mSearch->priceMax($maxPrice);
        }

        if($onlyPremium){
            $mSearch->onlyPremium($onlyPremium);
        }

        if($onlyPhotos){
            $mSearch->withPicture($onlyPhotos);
        }


        $mSearch->addField('res.pk_i_id as id_imagem');
        $mSearch->addField('res.s_path as path');
        $mSearch->addField('res.s_extension as extension');
        $mSearch->addField('d.s_title as title');

        $mSearch->page($page);

        $items = $mSearch->doSearch(FALSE,TRUE);

        return $items;
    }

    /**
     * Get Items Search
     * list all or single by userid
     *
     * @url POST /items/pages
     */
    public function getNumberPages($category, $pattern) {

        $json = file_get_contents('php://input');
        $filter = json_decode($json);
        $pattern = $filter->sPattern;
        $regionId = $filter->regionId;
        $cityId = $filter->cityId;
        $categoryId = $filter->categoryId;
        $minPrice = $filter->minPrice;
        $maxPrice = $filter->maxPrice;
        $onlyPremium = $filter->onlyPremium;
        $onlyPhotos = $filter->onlyPhotos;

        $mSearch = new Search();

        if($pattern){
            $mSearch->addPattern($pattern);
        }

        if($regionId){
            $mSearch->addRegion($regionId);
        }

        if($cityId){
            $mSearch->addCity($cityId);
        }

        if($categoryId){
            $mSearch->addCategory($categoryId);
        }

        if($minPrice){
            $mSearch->priceMin($minPrice);
        }

        if($maxPrice){
            $mSearch->priceMax($maxPrice);
        }

        if($onlyPremium){
            $mSearch->onlyPremium($onlyPremium);
        }

        if($onlyPhotos){
            $mSearch->withPicture($onlyPhotos);
        }

        $items = $mSearch->count();

        $number_pages = (int) ($items / 10);

        if($items % 10 > 0){
            $number_pages ++;
        }
        return $number_pages;
    }
}
