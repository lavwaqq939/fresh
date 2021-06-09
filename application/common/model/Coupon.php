<?php
/**
 * Created by PhpStorm.
 * User: 17828
 * Date: 2018/7/9
 * Time: 10:28
 */
namespace app\common\model;
use think\Model;

class Coupon extends Model
{
     public function getDoCoupon($where)
     {
         $coupon = db('user_coupon')
             ->alias('ucr')
             ->field("ucr.coupon_id,c.coupon_name,c.full_money,c.less_money,c.end_time,ucr.is_use")
             ->join('coupon c',"c.coupon_id = ucr.coupon_id")
             ->where($where)
             ->group("ucr.coupon_id")
             ->order('ucr.coupon_id desc')
             ->select();
         return $coupon;
     }
}