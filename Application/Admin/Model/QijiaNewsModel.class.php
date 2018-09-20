<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/26
 * Time: 15:44
 */
namespace Admin\Model;
use Think\Model;
class QijiaNewsModel extends Model
{
    protected $_validate = array(
        array('title','require','标题不能为空',0,'regex',3),
        array('admin_id','require','管理员ID不能为空',0,'regex',3),
        array('type','require','类型不能为空',0,'regex',3)
    );

    //添加之前
    protected function _before_insert(&$data,$options)
    {
        if($data['img']!='')
        {
            $original = $data['img'];
            $image = new \Think\Image();
            $image->open($original);
            // 按照原图的比例生成一个最大为40*14的缩略图并保存为thumb.jpg
            $image->thumb(1920,1200,\Think\Image::IMAGE_THUMB_CENTER)->save($original);
            $data['img'] = $original;
        }

    }

    protected function _before_update(&$data,$options) {
        //删除老图片
        if($data['img']!='') {
            $original = $data['img'];
            $image = new \Think\Image();
            $image->open($original);
            // 按照原图的比例生成一个最大为40*14的缩略图并保存为thumb.jpg
            $image->thumb(1920,1200,\Think\Image::IMAGE_THUMB_CENTER)->save($original);
            $data['img'] = $original;
            $oldPath = $this->where($options['where'])->getField('img');
            if($oldPath){
                if(file_exists($oldPath)){
                    @unlink($oldPath);
                }
            }
        }
    }

    protected function _before_delete($options) {
        $oldPath = $this->where($options['where'])->field('img')->select();
        foreach($oldPath as $key=>$val) {
            @unlink($val['img']);
        }
    }
}