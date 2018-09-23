<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/26
 * Time: 10:12
 */

namespace Admin\Controller;


use Think\Exception;
class ContentController extends CommonController
{
    public function index() {
        echo 'OK OK TEST';
    }
//分类列表
    public function cateList() {
        $this->list = M('Cate')->select();
        $this->display();
    }
//添加分类
    public function addCate() {
        $this->display();
    }
//添加分类AJAX
    public function addCateAjax() {
        if(IS_AJAX) {
            $data['cate_name'] = I('post.cate_name');
            $data['desc'] = I('post.desc');
            $data['create_time'] = time();
            $data['update_time'] = time();
            $res = M('Cate')->add($data);
            if($res) {
                $data['id'] = $res;
                json(array('code'=>1,'data'=>$data));
            }else {
                json(array('code'=>-1,'data'=>'添加数据库失败'));
            }
        }
    }
//修改分类
    public function modCate() {
        $id = I('get.id');
        $this->info = M('Cate')->where(array('id'=>$id))->find();
        $this->display();
    }
//修改分类AJAX
    public function modCateAjax() {
        if(IS_AJAX) {
            $data['id'] = I('post.cate_id');
            $data['cate_name'] = I('post.cate_name');
            $data['desc'] = I('post.desc');
            $data['update_time'] = time();
            $res = M('Cate')->save($data);
            if($res !== false) {
                $data['id'] = $res;
                json(array('code'=>1,'data'=>$data));
            }else {
                json(array('code'=>-1,'data'=>'添加数据库失败'));
            }
        }
    }
//删除分类
    public function delCate() {
        //TODO
    }
//文章列表
    public function articleList() {
        $search = I('get.search');
        $cate_id = I('get.cate_id');
        $where = array();
        if($cate_id) { $where['a.cate_id'] = $cate_id;}
        if($search) { $where['a.title'] = array('LIKE',"%" . $search . "%");}

        vendor('Page.page');
        $num = 10;
        $count = M('Article')->alias('a')->where($where)->count();
        $Page = new \Page($count,$num,5);
        $this->page = $Page->fpage(4,5,6);
        $this->pages = ceil($count/$num);

        $this->list = M('Article')->alias('a')
            ->join('left join j_cate c on a.cate_id=c.id')
            ->join('left join j_admin ad on a.aid=ad.id')
            ->where($where)
            ->field('a.*,c.cate_name,ad.realname')
            ->order(array('sort'=>'ASC'))
            ->limit($Page->start()-1,$Page->cnums())
            ->select();
        $this->catelist = M('Cate')->select();
        $this->display();
    }
//添加文章
    public function addArticle() {
        if(IS_POST) {
            $token = I('post.TOKEN');
            if(!checkToken($token)) {
                $this->redirect('Content/addArticle',array(),3,'不可重复提交表单,正在返回表单页面...');
            }
            $info = $this->multi_upload();
            if($info['error'] == 1) {
                $this->error($info['msg']);
            }

            $data['cover'] = serialize($info['data']);
            $data['title'] = I('post.title');
            $data['desc'] = I('post.desc');
            $data['content'] = I('post.content');
            $data['cate_id'] = I('post.cate_id');
            $data['aid'] = session('admin_id');
            $data['is_recommend'] = I('post.is_recommend');
            $data['is_recommend'] = $data['is_recommend'] ? $data['is_recommend'] : 0;
            $data['create_time'] = time();
            $data['update_time'] = time();

            $res = M('Article')->add($data);
            if($res) {
                $this->success('添加成功',U('Content/articleList'));
                exit();
            }else {
                $this->error('添加失败');
            }
        }
        $this->catelist = M('Cate')->select();
        createToken();
        $this->display();
    }
//修改文章
    public function modArticle() {
        if(IS_POST) {
            $token = I('post.TOKEN');
            if(!checkToken($token)) {
                $this->redirect('Content/modArticle',array('id'=>I('post.article_id')),3,'不可重复提交表单,正在返回表单页面...');
            }

            $exist = M('Article')->where(array('id'=>I('post.article_id')))->find();
            if(!$exist) {
                $this->error('非法操作ID');
            }

            $info = $this->multi_upload();
            if($info['error'] == 1) {
                $this->error($info['msg']);
            }

            $cover = I('post.cover') ? I('post.cover') : array();
            $cover_arr = array_merge($cover,$info['data']);
            if(count($cover_arr) > 3) {
                foreach ($cover_arr as $v) {
                    @unlink($v);
                }
                $this->error('非法操作COVER');
            }

            $old_cover = unserialize($exist['cover']);
            foreach ($old_cover as $k=>$v) {
                if(!in_array($v,$cover_arr)) {
                    @unlink($v);
                }
            }

            $data['id'] = I('post.article_id');
            $data['cover'] = serialize($cover_arr);
            $data['title'] = I('post.title');
            $data['desc'] = I('post.desc');
            $data['content'] = I('post.content');
            $data['cate_id'] = I('post.cate_id');
            $data['aid'] = session('admin_id');
            $data['is_recommend'] = I('post.is_recommend');
            $data['is_recommend'] = $data['is_recommend'] ? $data['is_recommend'] : 0;
            $data['update_time'] = time();

            $res = M('Article')->save($data);
            if($res !== false) {
                $this->success('保存成功',U('Content/articleList'));
                exit();
            }else {
                $this->error('保存失败');
            }
        }

        $this->info = M('Article')->where(array('id'=>I('get.id')))->find();
        $this->cover = unserialize($this->info['cover']);
        $this->catelist = M('Cate')->select();
        createToken();
        $this->display();
    }
//删除文章
    public function delArticle() {
        $article_id = I('post.article_id');
        if(IS_AJAX) {
            $exist = M('Article')->where(array('id'=>$article_id))->find();
            if(!$exist) {
                json(array('code'=>-1,'msg'=>'非法参数'));
            }
            $cover_arr = unserialize($exist['cover']);
            foreach ($cover_arr as $k=>$v) {
                @unlink($v);
            }

//            $ueditor_imgs = getPicFromUeditor($exist['content']);
//            foreach ($ueditor_imgs as $v) {
//                @unlink($v);
//            }
            $res = M('Article')->where(array('id'=>$article_id))->delete();
            if($res) {
                json(array('code'=>1,'msg'=>'删除成功'));
            }else {
                json(array('code'=>-1,'msg'=>'删除失败'));
            }
        }
    }
//文章排序
    public function sortArticle() {
        $data = array(
            'sort' => I('post.sort'),
        );
        $res = M('Article')->where(array('id'=>I('post.id')))->save($data);
        if($res) {
            json(1);
        }else {
            json(-1);
        }
    }
//是否推荐
    public function ifrecommend() {
        $result = M('Article')->where(array('id'=>I('post.id')))->save(array('is_recommend'=>I('post.is_recommend')));
        if($result) {
            json(1);
        }else {
            json(-1);
        }
    }


    public function settledList() {
        $search = I('get.search');
        $where = array();
        if($search) { $where['name'] = array('LIKE',"%" . $search . "%");}

        vendor('Page.page');
        $num = 10;
        $count = M('Settled')->where($where)->count();
        $Page = new \Page($count,$num,5);
        $this->page = $Page->fpage(4,5,6);
        $this->pages = ceil($count/$num);
        $this->list = M('Settled')->where($where)->limit($Page->start()-1,$Page->cnums())->select();
        $this->display();
    }

    public function addCo() {
        if(IS_POST) {
            $token = I('post.TOKEN');
            if(!checkToken($token)) {
                $this->redirect('Content/addCo',array(),3,'不可重复提交表单,正在返回表单页面...');
            }
            $info = $this->multi_upload();
            if($info['error'] == 1) {
                $this->error($info['msg']);
            }

            $data['cover'] = serialize($info['data']);
            $data['name'] = I('post.name');
            $data['desc'] = I('post.desc');
            $data['content'] = I('post.content');
            $data['aid'] = session('admin_id');
            $data['is_recommend'] = I('post.is_recommend');
            $data['is_recommend'] = $data['is_recommend'] ? $data['is_recommend'] : 0;
            $data['create_time'] = time();
            $data['update_time'] = time();

            $res = M('Settled')->add($data);
            if($res) {
                $this->success('添加成功',U('Content/settledList'));
                exit();
            }else {
                $this->error('添加失败');
            }
        }
        createToken();
        $this->display();
    }

    public function modCo() {
        if(IS_POST) {
            $token = I('post.TOKEN');
            if(!checkToken($token)) {
                $this->redirect('Content/modCo',array('id'=>I('post.company_id')),3,'不可重复提交表单,正在返回表单页面...');
            }
            $exist = M('Settled')->where(array('id'=>I('post.company_id')))->find();
            if(!$exist) {
                $this->error('非法操作ID');
            }

            $info = $this->multi_upload();
            if($info['error'] == 1) {
                $this->error($info['msg']);
            }

            $cover = I('post.cover') ? I('post.cover') : array();
            $cover_arr = array_merge($cover,$info['data']);
            if(count($cover_arr) > 3) {
                foreach ($cover_arr as $v) {
                    @unlink($v);
                }
                $this->error('非法操作COVER');
            }

            $old_cover = unserialize($exist['cover']);
            foreach ($old_cover as $k=>$v) {
                if(!in_array($v,$cover_arr)) {
                    @unlink($v);
                }
            }

            $data['id'] = I('post.company_id');
            $data['cover'] = serialize($cover_arr);
            $data['name'] = I('post.name');
            $data['desc'] = I('post.desc');
            $data['content'] = I('post.content');
            $data['aid'] = session('admin_id');
            $data['is_recommend'] = I('post.is_recommend');
            $data['is_recommend'] = $data['is_recommend'] ? $data['is_recommend'] : 0;
            $data['update_time'] = time();

            $res = M('Settled')->save($data);
            if($res !== false) {
                $this->success('保存成功',U('Content/settledList'));
                exit();
            }else {
                $this->error('保存失败');
            }
        }
        $this->info = M('Settled')->where(array('id'=>I('get.id')))->find();
        $this->cover = unserialize($this->info['cover']);
        createToken();
        $this->display();
    }

    public function delCo() {
        $company_id = I('post.company_id');
        if(IS_AJAX) {
            $exist = M('Settled')->where(array('id'=>$company_id))->find();
            if(!$exist) {
                json(array('code'=>-1,'msg'=>'非法参数'));
            }
            $cover_arr = unserialize($exist['cover']);
            foreach ($cover_arr as $k=>$v) {
                @unlink($v);
            }

//            $ueditor_imgs = getPicFromUeditor($exist['content']);
//            foreach ($ueditor_imgs as $v) {
//                @unlink($v);
//            }
            $res = M('Settled')->where(array('id'=>$company_id))->delete();
            if($res) {
                json(array('code'=>1,'msg'=>'删除成功'));
            }else {
                json(array('code'=>-1,'msg'=>'删除失败'));
            }
        }
    }

    public function sortCo() {
        $data = array(
            'sort' => I('post.sort'),
        );
        $res = M('Settled')->where(array('id'=>I('post.id')))->save($data);
        if($res) {
            json(1);
        }else {
            json(-1);
        }
    }

    public function ifrecommendCo() {
        $result = M('Settled')->where(array('id'=>I('post.id')))->save(array('is_recommend'=>I('post.is_recommend')));
        if($result) {
            json(1);
        }else {
            json(-1);
        }
    }

    public function visit() {
        $id = 1;
        $this->info = M('Visit')->where(array('id'=>$id))->find();
        $this->display();
    }

    public function visitAjax() {
        if(IS_AJAX) {
            $data['title'] = I('post.title');
            $data['visit_time'] = I('post.visit_time');
            $data['content'] = I('post.content');
            $id = 1;
            $exist = M('Visit')->where(array('id'=>$id))->find();
            if($exist) {
                $res = M('Visit')->where(array('id'=>$id))->save($data);
            }else {
                $res = M('Visit')->add($data);
            }
            if($res !== false) {
                json(1);
            }else {
                json('保存失败');
            }
        }
    }

    public function companyInfo() {
        $id = 1;
        $this->info = M('Company')->where(array('id'=>$id))->find();
        $this->lonlat = implode(',',array($this->info['lon'],$this->info['lat']));
        $this->display();
    }

    public function saveCompany() {
        if(IS_AJAX) {
            $data['name'] = I('post.name');
            $data['principal'] = I('post.principal');
            $data['tel'] = I('post.tel');
            $data['address'] = I('post.address');
            $data['email'] = I('post.email');
            $data['profile'] = I('post.profile');
            $lonlat = explode(',',I('post.lonlat'));
            $data['lon'] = $lonlat[0];
            $data['lat'] = $lonlat[1];
            $id = 1;
            $exist = M('Company')->where(array('id'=>$id))->find();
            if($exist) {
                $res = M('Company')->where(array('id'=>$id))->save($data);
            }else {
                $res = M('Company')->add($data);
            }
            if($res !== false) {
                json(1);
            }else {
                json('保存失败');
            }
        }
    }

    public function test() {
        $article_id = 1;
        $exist = M('Article')->where(array('id'=>$article_id))->find();
        $ueditor_imgs = getPicFromUeditor($exist['content']);
        foreach ($ueditor_imgs as $v) {
            @unlink($v);
        }
        dump($ueditor_imgs);die;

    }







}