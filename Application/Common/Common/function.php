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

//创建TOKEN
function createToken() {
    $code = chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE));
    session('TOKEN', authcode($code));
}

//判断TOKEN
function checkToken($token) {
    if ($token == session('TOKEN')) {
        session('TOKEN', NULL);
        return TRUE;
    } else {
        return FALSE;
    }
}

/* 加密TOKEN */
function authcode($str) {
    $key = "Jianghairui";
    $str = substr(md5($str), 8, 10);
    return md5($key . $str);
}


function getPicFromUeditor($content){
    if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
        $img_arr = $matches[3];
        $arr = [];
        for($i=0;$i<3;$i++) {
            if (preg_match('/\/Public\/ueditor\/php/', $img_arr[$i])) {
                $arr[] = substr($img_arr[$i],7);
            }
        }
        return $arr;
    }
}

function create_unique_number($letter = '')
{
    $time = explode (" ", microtime ());
    $timeArr = explode('.',$time [0]);
    $mtime = array_pop($timeArr);
    $fulltime = $letter.$time[1].$mtime;
    return $fulltime;
}