<?php
/**
 * Created by PhpStorm.
 * User: 17828
 * Date: 2018/7/6
 * Time: 15:40
 */
namespace app\common\model;
use think\Model;

class User extends Model
{
    public function getUserInfo($user_id)
    {
        $data = db('user')->alias('u')
            ->field("u.nickname,u.headimg,u.telephone,u.create_time,ui.account,ui.vip_end_time")
            ->join('user_info ui','u.user_id = ui.user_id')
            ->where('u.user_id', $user_id)
            ->where('status',1)
            ->find();
        return $data;
    }
}