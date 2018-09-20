<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/26
 * Time: 10:27
 */

namespace Admin\Controller;

use Think\Controller;
class LoginController extends Controller {
    public function index() {
//        $list = M('Admin')->select();
//        dump($list);die;
        if(session('qijiaxueche_username')) {
            $this->redirect('Index/index');
            return;
        }
        if(cookie('qijiaxueche_pwd') != '') {
            $this->moviecard_username = cookie('qijiaxueche_username');
            $this->moviecard_pwd = cookie('qijiaxueche_pwd');
        }
        $this->display();
    }

    public function login() {
        $Admin = M('Admin');
        if(IS_POST) {
            $where['username'] = $_POST['username'];
            $where['password'] = md5(I('post.password'));
            $result = $Admin->where($where)->find();
            if($result) {
                session('qijiaxueche_username',1);
                session('admin_id',$result['id']);
                session('username',$result['username']);
                session('nickname',$result['nickname']);

                if($_POST['remember_pwd'] == 1) {
                    cookie('qijiaxueche_username',I('post.username'),3600*24*7);
                    cookie('qijiaxueche_pwd',I('post.password'),3600*24*7);
                }else {
                    cookie('qijiaxueche_username',null);
                    cookie('qijiaxueche_pwd',null);
                }

                $this->success('登录成功',U('Index/index'));
            }else {
                $this->error('用户名密码不匹配');
            }
        }
    }

    public function frozen() {
        $this->display();
    }

    public function logout() {
        session(null);
        $this->redirect('Login/index');
    }
}