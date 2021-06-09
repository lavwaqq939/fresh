<?php
/**
 * Created by PhpStorm.
 * User: 17828
 * Date: 2018/5/31
 * Time: 14:23
 */
namespace app\common\validate;


use think\Validate;

class User extends Validate
{
    protected $rule = [
        "telephone" => "require",
        "code" => 'require',

        "user_id" => 'require',
        "username" => 'require',
        "area_name" => 'require',
        "address" => 'require',
        "longitude" => 'require',
        "latitude" => 'require',

        "payTelephone"=>"require|regex:tel|exitsTelephone",
        "pay_password"=>"require|confirmPay",
        "qrpay_password"=>"require",
        "pay"=>"require|checkPay",

    ];
    protected $message = [
        "telephone.require" => "请填写手机号",
        "code.require" => "请填写验证码",

        'user_id.require'  => '用户id不可为空',
        'username.require'  => '用户名不可为空',
        'area_name.require'  => '收货地址不可为空',
        'address.require'  => '详细地址不可为空',
        'longitude.require'  => '缺少参数',
        'latitude.require'  => '缺少参数',

        "pay_password.require"=>"请输入6位数字支付密码",
        "qrpay_password.require"=>"请输入支付密码",
        "pay.require"=>"请填写支付密码",
        "payTelephone.require"=>"请填写手机号",
        "payTelephone.regex"=>"手机号格式不正确",
    ];
    protected $regex = [
        "tel"=>'/^1[3456789]\d{9}$/',
    ];
    protected $scene = [
        "checkCode" => "telephone,code",
        "checkaddAddress" => "telephone,user_id,username,area_name,address,longitude,latitude",

        "pay"=>"pay,user_id",
        "addPayPassword"=>"pay_password,qrpay_password,user_id",
        "revisePayPassword"=>"pay,pay_password,qrpay_password,user_id",
        "forgetPayPassword"=>"pay_password,qrpay_password,user_id",
    ];




    //判断手机号是否存在
    public function exitsTelephone($value){
        $where = [
            "telephone"=>$value
        ];
        $find = db("user")->where($where)->find();
        if(!$find) return "账号不存在";
        if($find["status"]!=1) return "账号异常,请联系客服";
        return true;
    }
    //验证支付密码
    public function checkPay($value){
        $where = [
            "pay_password"=>$value,
            "user_id"=>input("user_id")
        ];
        $find = db("user")->where($where)->find();
        if(!$find) return "支付密码错误了。";
        return true;
    }
    //确认支付密码
    public function confirmPay($value){
        if($value != input("qrpay_password")){
            return "密码不一致";
        }
        return true;
    }
}