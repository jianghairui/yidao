<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/26
 * Time: 15:44
 */
namespace Admin\Model;
use Think\Model;
class QijiaContactModel extends Model
{
    protected $_validate = array(
        array('linkman','require','联系人不能为空',0,'regex',3),
        array('tel','require','手机号不能为空',0,'regex',3),
        array('tel','/^0?(13|14|15|17|18)[0-9]{9}$/','手机号不存在',0,'regex',3),
        array('city','require','所属城市不能为空',0,'regex',3),
        array('address','require','地址不能为空',0,'regex',3),
        array('registration','require','备案号不能为空',0,'regex',3),
        array('email','require','邮箱地址不能为空',0,'regex',3),
        array('email','/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/','邮箱地址格式不符',0,'regex',3),
    );

    protected function _before_update(&$data,$options) {
        //删除老图片
        if($data['qrcode']!='') {
            $oldPath = $this->where($options['where'])->getField('qrcode');
            if($oldPath){
                if(file_exists($oldPath)){
                    @unlink($oldPath);
                }
            }
        }
        if($data['course_qrcode']!='') {
            $oldPath = $this->where($options['where'])->getField('qrcode');
            if($oldPath){
                if(file_exists($oldPath)){
                    @unlink($oldPath);
                }
            }
        }
    }

    protected function _before_delete($options) {
        $oldPath = $this->where($options['where'])->field('qrcode')->select();
        foreach($oldPath as $key=>$val) {
            @unlink($val['qrcode']);
        }
    }
}