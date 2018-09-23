<?php
namespace Home\Controller;

class IndexController extends CommonController {
//首页
    public function index(){
        $where = array();
        $where['is_recommend'] = 1;
        $where['cate_id'] = 1;
        $this->list = M('Article')->where($where)->order(array('sort'=>'ASC'))->select();
        $this->display();
    }

    public function detail() {
        $id = I('get.id');
        $exist = M('Article')->where(array('id'=>$id))->find();
        if($exist) {
            $this->info = $exist;
        }else {
            $this->error('非法操作');
        }
        $this->display();
    }

    public function release() {
        $this->display();
    }

    public function about() {
        $this->display();
    }

    public function address() {
        $this->display();
    }

    public function company() {
        $this->display();
    }


    public function join() {
        $this->display();
    }

    public function others() {
        $this->display();
    }

    public function visit() {
        $this->display();
    }




    private function getip() {
        $unknown = 'unknown';
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /*
        处理多层代理的情况
        或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
        */
        if (false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));
        return $ip;
    }


}