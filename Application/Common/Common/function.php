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

function generateVerify($width,$height,$type,$length,$fontsize) {
    $image = imagecreatetruecolor($width,$height);
    $white = imagecolorallocate($image,255,255,255);
    imagefilledrectangle($image,0,0,$width,$height,$white);
    //匹配验证码字符类型
    switch($type) {
        case 0:
            $str = join('',array_rand(range(0,9),$length));
            break;
        case 1:
            $str = join('',array_rand(array_flip(array_merge(range('a','z'),range('A','Z'))),$length));
            break;
        case 2:
            $str = join('',array_rand(array_flip(array_merge(range('a','z'),range('A','Z'),range(0,9))),$length));
            break;
    }
    for($i=0;$i<$length;$i++) {
        imagettftext($image,$fontsize,mt_rand(-30,30),$i*($width/$length)+5,mt_rand(($height/2)+($fontsize/2),($height/2)+($fontsize/2)),randColor($image),'Public/fonts/PingFang-Regular.ttf',$str[$i]);
    }
    //添加像素点
    for ($i=1;$i<=100;$i++) {
        imagesetpixel($image,mt_rand(0,$width),mt_rand(0,$height),randColor($image));
    }
    //输出后销毁图片
    header('Content-Type:image/png');
    imagepng($image);
    imagedestroy($image);
    return $str;
}

function randColor($image) {
    return imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
}