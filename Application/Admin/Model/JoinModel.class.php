<?php
/**
 * Created by PhpStorm.
 * User: 凯拓
 * Date: 2018/1/26
 * Time: 15:44
 */
namespace Admin\Model;
use Think\Model;
class JoinModel extends Model
{
    protected $_validate = array(
        array('title','require','标题不能为空',0,'regex',3),
        array('admin_id','require','管理员ID不能为空',0,'regex',3)
    );

    //添加之前
    protected function _before_insert(&$data,$options)
    {
        if($data['image']!='')
        {
//            $original = $data['image'];
//            $image = new \Think\Image();
//            $image->open($original);
            // 按照原图的比例生成一个最大为40*14的缩略图并保存为thumb.jpg
//            $image->thumb(1920,1200,\Think\Image::IMAGE_THUMB_CENTER)->save($original);
//            $data['image'] = $original;
        }

    }

    protected function _before_update(&$data,$options) {
        if($data['image']!='') {
//            $original = $data['image'];
//            $image = new \Think\Image();
//            $image->open($original);
//            $image->thumb(1920,1200,\Think\Image::IMAGE_THUMB_CENTER)->save($original);
//            $data['image'] = $original;
            $oldPath = $this->where($options['where'])->getField('image');
            if($oldPath){
                if(file_exists($oldPath)){
                    @unlink($oldPath);
                }
            }
        }
    }

    protected function _before_delete($options) {
        $oldPath = $this->where($options['where'])->field('image')->select();
        foreach($oldPath as $key=>$val) {
            @unlink($val['image']);
        }
    }
}