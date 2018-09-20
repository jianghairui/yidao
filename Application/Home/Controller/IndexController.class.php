<?php
namespace Home\Controller;

class IndexController extends CommonController {
//首页
    public function index(){
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $where = array();
        $this->pvuvStatistics();
        $this->slide = M('Slideshow')
            ->where(array('is_recommend'=>1))
            ->order(array('ctime'=>'ASC'))->select();
        $this->env = M('News')->where(array('type'=>4))->select();
        $limit = '0,4';
        $this->productList = M('Join')->alias('j')
            ->where($where)
            ->order(array('sort'=>'ASC'))
            ->limit($limit)
            ->select();
        $this->display('Index/Mobile/index');
    }

    public function productList() {
        $this->productList = M('Join')->alias('j')
            ->order(array('id'=>'DESC'))
            ->select();
        $this->display('Index/Mobile/productList');
    }
//了解更多
    public function product_detail() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $this->info = M('Join')->alias('j')
            ->where(array('j.id'=>I('get.id')))
            ->find();
        $this->display('Index/Mobile/product_detail');
    }
//驾校课程
    public function curriculum_erweima() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $this->courseList = M('Join')->alias('j')
            ->join("LEFT JOIN ims_acti2_cat c ON j.pid=c.id")
            ->field('j.*,c.cat_name')
            ->select();
        $this->display('Index/Mobile/curriculum_erweima');
    }
//企业历程
    public function enterprise_experience() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $this->display('Index/Mobile/enterprise_experience');
    }
//行业动态
    public function Industry_dynamics() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $limit = '0,4';
        if(I('get.act') == 'history') {
            $limit = '0,100';
        }
        $this->list = M('News')
            ->order(array('id'=>'DESC'))
            ->limit($limit)
            ->where(array('type'=>2,'is_recommend'=>1))->select();
        $this->display('Index/Mobile/industry_dynamics');
    }

    public function dynamic_details() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $this->info = M('News')->alias('n')
            ->join("LEFT JOIN ims_acti2_admin a ON n.admin_id=a.id")
            ->field('n.*,a.nickname')
            ->where(array('n.id'=>I('get.id')))->find();
        $this->display('Index/Mobile/dynamic_details');
    }
//交通法规
    public function traffic_regulations() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $this->list = M('News')->where(array('type'=>3,'is_recommend'=>1))->select();
        $this->display('Index/Mobile/traffic_regulations');
    }

    public function details_of_traffic_regulations() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $this->info = M('News')->alias('n')
            ->join("LEFT JOIN ims_acti2_admin a ON n.admin_id=a.id")
            ->field('n.*,a.nickname')
            ->where(array('n.id'=>I('get.id')))->find();
        $this->display('Index/Mobile/details_of_the_laws');
    }
//联系我们
    public function contact_us() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $this->display('Index/Mobile/contact_us');
    }

    public function apply() {
        $Apply = M('Apply');
        $exist = $Apply->where(array('tel'=>I('post.tel')))->find();
        if($exist) {
            json('此号码已预约,请使用其他手机号');
        }
        $_POST['ip'] = $this->getip();
        if($Apply->add($_POST)) {
            json(1);
        }else {
            json('提交失败');
        }
    }

    public function pvuvStatistics() {
        if(!$this->is_weixin()) {
            $this->error('请用微信打开网页');
        }
        $pvuv = M('Pvuv');
        $exist = $pvuv->where(array('ip'=>$this->getip(),'_string'=>'TO_DAYS(vtime)=TO_DAYS(NOW())'))->find();
        if(!$exist) {
            $pvuv->add(array('client_ip'=>$this->getip()));
        }
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