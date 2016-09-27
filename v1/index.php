<?php
require_once '../../oc-load.php';        
require_once '../RestServer.php';
require_once '../ItemDAO.php';
require_once '../CategoryDAO.php';
require_once '../UserDAO.php';
require_once '../MapDAO.php';
require_once '../RegionDAO.php';
require_once 'ServerAPI.php';

	if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

$server = new RestServer('debug');

$server->addClass('ServerAPI');

$server->handle();

