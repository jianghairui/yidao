<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/26
 * Time: 10:13
 */

namespace Admin\Controller;


use Think\Controller;

class CommonController extends Controller
{
    public function _initialize() {

        if(!session('shandayidao_username')) {
            $this->error("请登录系统后操作",U('Login/index'));
        }

        $this->controller_name = CONTROLLER_NAME;
        $this->action_name = ACTION_NAME;
        $this->full_url = CONTROLLER_NAME . '/' . ACTION_NAME;
    }

    //上传单张图片方法
    public function OneUpload($File) {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
        $upload->rootPath  =      './'; // 设置附件上传根目录
        $upload->savePath = './Public/Uploads/';
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES[$File]);
        if(!$info) {// 上传错误提示错误信息
            $this->ajaxReturn($upload->getError());
        }else{// 上传成功 获取上传文件信息
            return $info;
        }
    }

    public function multi_upload() {
        foreach ($_FILES as $k=>$v) {
            if($v['name'] == '') {
                unset($_FILES[$k]);
            }else {
                if($this->checkfile($k) !== true) {
                    return array('error'=>1,'msg'=>$this->checkfile($k));
                }
            }
        }
        $arr = array();
        if(count($arr) > 3) {
            return array('error'=>1,'msg'=>'图片不可超过三张');
        }
        foreach ($_FILES as $k=>$v) {
            $filename_array = explode('.',$_FILES[$k]['name']);
            $ext = array_pop($filename_array);

            $path =  'Public/Uploads/' . date('Y-m-d');
            is_dir($path) or mkdir($path,0777,true);
            //转移临时文件
            $newname = create_unique_number() . '.' . $ext;
            move_uploaded_file($_FILES[$k]["tmp_name"], $path . "/" . $newname);
            $arr[] = $path . "/" . $newname;
        }
        return array('error'=>0,'data'=>$arr);
    }
//检验格式大小
    private function checkfile($file) {
        $allowType = array(
            "image/gif",
            "image/jpeg",
            "image/png",
            "image/pjpeg",
            "image/bmp"
        );
        if(!in_array($_FILES[$file]["type"],$allowType)) {
            return 'invalid fileType :' . $_FILES[$file]["name"];
        }
        if($_FILES[$file]["size"] > 1024*512) {
            return 'fileSize not exceeding  512Kb :' . $_FILES[$file]["name"];
        }
        if ($_FILES[$file]["error"] > 0) {
            return "error: " . $_FILES[$file]["error"];
        }else {
            return true;
        }
    }
}