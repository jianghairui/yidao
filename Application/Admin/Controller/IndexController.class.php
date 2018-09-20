<?php
namespace Admin\Controller;

class IndexController extends CommonController {

    public function index(){
        vendor('Page.page','','.php');
        $Apply = M('Apply');
        $pvuv = M('Pvuv');
        $this->count = $Apply->count();
        $Page = new \Page($this->count,10,5);
        $this->page = $Page->fpage(4,5,6);
        $this->list = $Apply->limit($Page->start()-1,$Page->cnums())->select();
        $this->total_pvuv = $pvuv->count();
        $this->today_pvuv = $pvuv->where(array('_string'=>'TO_DAYS(vtime)=TO_DAYS(NOW())'))->count();
        $this->display();
    }
}