<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/29
 * Time: 14:49
 */
function json($data)
{
    header('Content-Type:application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Cache-Control");
    header('Access-Control-Allow-Methods: GET, POST, PUT');
    exit(json_encode($data));
}