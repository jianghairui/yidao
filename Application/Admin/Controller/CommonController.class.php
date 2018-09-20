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

        if(!session('qijiaxueche_username')) {
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
}