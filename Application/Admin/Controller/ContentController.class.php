<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/26
 * Time: 10:12
 */

namespace Admin\Controller;


class ContentController extends CommonController
{
    public function index() {
        echo 'OK OK TEST';
    }

    public function courseList() {
        $search = I('get.search');
        $where = array();
        if($search) { $where['n.title'] = array('LIKE',"%" . $search . "%");}
        $this->list = M('News')->alias('n')
            ->join("LEFT JOIN ims_acti2_admin a ON n.admin_id=a.id")
            ->where($where)
            ->field('n.*,a.nickname')
            ->order(array('n.sort'=>'ASC'))
            ->where(array('type'=>1))->select();
        $this->type = 1;
        $this->title = '企业历程';
        $this->display();
    }

    public function newsList() {
        $search = I('get.search');
        $where['n.type'] = array('EQ',2);
        if($search) { $where['n.title'] = array('LIKE',"%" . $search . "%");}
        $this->list = M('News')->alias('n')
            ->join("LEFT JOIN ims_acti2_admin a ON n.admin_id=a.id")
            ->field('n.*,a.nickname')
            ->order(array('n.sort'=>'ASC'))
            ->where($where)->select();
        $this->type = 2;
        $this->title = '行业动态';
        $this->display('courseList');
    }

    public function productList() {
        $search = I('get.search');
        $where = array();
        if($search) { $where['title|desc'] = array('LIKE',"%" . $search . "%");}
        $this->list = M('Join')
            ->field('content',true)
            ->order(array('sort'=>'ASC'))
            ->where($where)->select();
        $this->type = 3;
        $this->title = '产品介绍';
        $this->display('productList');
    }

    public function addProduct() {
        $this->typename = '产品';
        $this->display();
    }

    public function addProductPost() {
        $data['title'] = I('post.title');
        $data['desc'] = I('post.desc');
        $data['content'] = I('post.content');
        $data['admin_id'] = session('admin_id');
        $data['date'] = time();
        $News = D('Join');
        if($_FILES['file']['name'] != '') {
            $info = $this->OneUpload('file');
            $data['image'] = $info['savepath'].$info['savename'];
        }

        $result = $News->add($data);
        if($result !== false) {
            $this->success('添加成功',U('Content/productList'));
        }else {
            $this->error('添加失败');
        }
    }

    public function modProduct() {
        $content_id = I('get.content_id');
        $info = M('Join')->where(array('id'=>$content_id))->find();
        if($info) {
            $this->info = $info;
        }else {
            $this->error('系统错误');
        }
        $this->typename = '产品';
        $this->content_id = $content_id;
        $this->display();
    }

    public function modProductPost() {
        $data['title'] = I('post.title');
        $data['desc'] = I('post.desc');
        $data['content'] = I('post.content');
        $data['updater_id'] = session('admin_id');
        $data['lastdate'] = time();
        $data['id'] = I('post.content_id');
        $Join = D('Join');
        if($_FILES['file']['name'] != '') {
            $info = $this->OneUpload('file');
            $data['image'] = $info['savepath'].$info['savename'];
        }
        $result = $Join->save($data);
        if($result !== false) {
            $this->success('修改成功',U('Content/productList'));
        }else {
            $this->error('修改失败');
        }
    }

    public function delProduct() {
        $id = I('get.content_id');
        $res = D('Join')->where(array('id'=>$id))->delete();
        if($res) {
            $this->success('删除成功');
        }else {
            $this->error('删除失败');
        }
    }

    public function environment() {
        $search = I('get.search');
        $where['n.type'] = array('EQ',4);
        if($search) { $where['n.title'] = array('LIKE',"%" . $search . "%");}
        $this->list = M('News')->alias('n')
            ->join("LEFT JOIN ims_acti2_admin a ON n.admin_id=a.id")
            ->field('n.*,a.nickname')
            ->order(array('n.sort'=>'ASC'))
            ->where($where)->select();
        $this->type = 4;
        $this->title = '驾校环境';
        $this->display('courseList');
    }

    public function addContent() {
        $type = I('get.type');
        in_array($type,array(1,2,3,4)) || $this->error('非法参数');
        switch($type) {
            case 1:$typename = '企业历程';break;
            case 2:$typename = '行业动态';break;
            case 3:$typename = '产品';break;
            case 4:$typename = '驾校环境';break;
        }
        $this->type = $type;
        $this->typename = $typename;
        $this->display();
    }

    public function addContentPost() {
        $data['title'] = I('post.title');
        $data['desc'] = I('post.desc');
        $data['content'] = I('post.content');
        $data['type'] = I('post.type');
        $data['admin_id'] = session('admin_id');
        $News = D('News');
        if($_FILES['file']['name'] != '') {
            $info = $this->OneUpload('file');
            $data['img'] = $info['savepath'].$info['savename'];
        }
        switch($data['type']) {
            case 1:$url = 'Content/courseList';break;
            case 2:$url = 'Content/newsList';break;
            case 3:$url = 'Content/lawsList';break;
            case 4:$url = 'Content/environment';break;
        }
        $result = $News->add($data);
        if($result !== false) {
            $this->success('添加成功',U($url));
        }else {
            $this->error('添加失败');
        }
    }

    public function modContent() {
        $id = I('get.content_id');
        $this->info = M('News')->where(array('id'=>$id))->find();
        switch($this->info['type']) {
            case 1:$typename = '企业历程';break;
            case 2:$typename = '行业动态';break;
            case 3:$typename = '交通法规';break;
            case 4:$typename = '驾校环境';break;
        }
        $this->typename = $typename;
        $this->content_id = $id;
        $this->display();
    }

    public function modContentPost() {
        $data['title'] = I('post.title');
        $data['desc'] = I('post.desc');
        $data['content'] = I('post.content');
        $data['admin_id'] = session('admin_id');
        $data['type'] = I('post.type');
        $id = I('post.content_id');
        $News = D('News');
        if($_FILES['file']['name'] != '') {
            $info = $this->OneUpload('file');
            $data['img'] = $info['savepath'].$info['savename'];
        }
        switch($data['type']) {
            case 1:$url = 'Content/courseList';break;
            case 2:$url = 'Content/newsList';break;
            case 3:$url = 'Content/lawsList';break;
            case 4:$url = 'Content/environment';break;
        }
        $result = $News->where(array('id'=>$id))->save($data);
        if($result !== false) {
            $this->success('修改成功',U($url));
        }else {
            $this->error('修改失败');
        }
    }

    public function delContent() {
        $id = I('get.content_id');
        $res = D('News')->where(array('id'=>$id))->delete();
        if($res) {
            $this->success('删除成功');
        }else {
            $this->error('删除失败');
        }
    }

    public function sort() {
        $sort = I('post.sort');
        $id = I('post.id');
        $res = M('News')->where(array('id'=>$id))->setField('sort',$sort);
        if($res !== false) {
            $this->ajaxReturn(1);
        }else {
            $this->ajaxReturn(-1);
        }
    }

    public function proSort() {
        $sort = I('post.sort');
        $id = I('post.id');
        $res = M('Join')->where(array('id'=>$id))->setField('sort',$sort);
        if($res !== false) {
            $this->ajaxReturn(1);
        }else {
            $this->ajaxReturn(-1);
        }
    }

    public function slideList() {
        $this->list = M('Slideshow')->order(array('ctime'=>'ASC'))->select();
        $this->display();
    }

    public function addSlide() {
        if(IS_AJAX) {
            if($_FILES['file']['name'] != '') {
                $info = $this->OneUpload('file');
                $_POST['pic'] = $info['savepath'].$info['savename'];
            }
            $Slide = D('Slideshow');
            if($Slide->create()) {
                if($Slide->add()) {
                    json(1);
                }else {
                    json('添加失败');
                }
            }else {
                json($Slide->getError());
            }
        }
        $this->display();
    }

    public function modSlide() {
        if(IS_AJAX) {
            if($_FILES['file']['name'] != '') {
                $info = $this->OneUpload('file');
                $_POST['pic'] = $info['savepath'].$info['savename'];
            }
            $Slide = D('Slideshow');
            if($Slide->create()) {
                if($Slide->save() !== false) {
                    json(1);
                }else {
                    json('保存失败');
                }
            }else {
                json($Slide->getError());
            }
        }
        $this->info = M('Slideshow')->where(array('id'=>I('get.id')))->find();
        $this->display();
    }

    public function delSlide() {
        $Slide = D('Slideshow');
        $id = I('get.id');
        $res = $Slide->where(array('id'=>$id))->delete();
        if($res) {
            $this->success('删除成功');
        }else {
            $this->error('删除失败');
        }
    }

    public function isrecommend() {
        $Slide = M('Slideshow');
        $id = I('post.id');
        $info = $Slide->where(array('id'=>$id))->find();
        if($info['is_recommend'] == 1) {
            $res = $Slide->where(array('id'=>$id))->setField('is_recommend',-1);
            if($res !== false) {
                json(1);
            }else {
                json('系统错误');
            }
        }else {
            $res = $Slide->where(array('id'=>$id))->setField('is_recommend',1);
            if($res !== false) {
                json(2);
            }else {
                json('系统错误');
            }
        }
    }

    public function newSort() {
        $wraper = M('Slideshow');
        $order = $wraper->order(array('ctime' => 'ASC'))->getField('ctime', true);

        $newArr = explode(',', $_POST['newid']);
        foreach ($newArr as $key => $v) {
            $data = array(
                'ctime' => $order[$key]
            );
            $wraper->where(array('id' => $v))->save($data);
        }

        $this->ajaxReturn(1);
    }

    public function contactInfo() {
        $this->info = M('Contact')->where(array('id'=>1))->find();
        $this->display();
    }

    public function saveContactInfo() {
        $Contact = D('Contact');
        $_POST['id'] = 1;
        if($_FILES['file']['name'] != '') {
            $info = $this->OneUpload('file');
            $_POST['qrcode'] = $info['savepath'].$info['savename'];
        }
        if($_FILES['file2']['name'] != '') {
            $info = $this->OneUpload('file2');
            $_POST['course_qrcode'] = $info['savepath'].$info['savename'];
        }
        if(IS_AJAX) {
            if($Contact->create()) {
                $res = $Contact->save();
                if($res !== false) {
                    $this->ajaxReturn(1);
                }else {
                    $this->ajaxReturn('保存失败');
                }
            }else {
                $this->ajaxReturn($Contact->getError());
            }
        }
    }




}