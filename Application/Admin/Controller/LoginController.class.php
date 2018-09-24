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
        $list = M('Admin')->select();
        if(session('shandayidao_username')) {
            $this->redirect('Index/index');
            return;
        }
        if(cookie('shandayidao_pwd') != '') {
            $this->shandayidao_username = cookie('shandayidao_username');
            $this->shandayidao_pwd = cookie('shandayidao_pwd');
        }
        $this->display();
    }

    public function login() {
        $Admin = M('Admin');
        if(IS_POST) {
            $login_vcode = I('post.login_vcode');
            if($login_vcode !== session('login_vcode')) {
                $this->error('验证码错误');
            }
            session('login_vcode',null);
            $where['username'] = $_POST['username'];
            $where['password'] = md5(I('post.password'));
            $result = $Admin->where($where)->find();
            if($result) {
                session('shandayidao_username',1);
                session('admin_id',$result['id']);
                session('username',$result['username']);
                session('nickname',$result['nickname']);

                if($_POST['remember_pwd'] == 1) {
                    cookie('shandayidao_username',I('post.username'),3600*24*7);
                    cookie('shandayidao_pwd',I('post.password'),3600*24*7);
                }else {
                    cookie('shandayidao_username',null);
                    cookie('shandayidao_pwd',null);
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

    public function verifyCode() {
        $vcode = generateVerify(200,50,2,4,24);
        session('login_vcode',$vcode);
    }

    public function setsession() {
//        session('vcode1','我是10秒的验证码啊');
//        session('vcode2','我是20秒的验证码啊');
    }

    public function getsession() {
        dump(session('login_vcode'));
        dump($_SESSION);
    }

    public function test() {
        session('a',array('A','B'));
//        phpinfo();
    }
}