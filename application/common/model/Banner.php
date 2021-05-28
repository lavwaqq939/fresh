<?php
namespace app\common\model;

use think\Model;

class Banner extends Model
{
    public function getBanner($where){
      $banner = db('banner')
          ->field('banner_id,link,img_path')
          ->where($where)
          ->where(function($query){
              $query->where('end_time','egt',date("Y-m-d"))->whereOr('end_time',null);
          })
          ->order('order_number desc')
          ->select();
      return $banner;
    }
}