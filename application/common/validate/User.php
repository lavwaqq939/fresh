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
        "feedback_cate_id" => 'require',
        "feedback_content" => 'require',
        'password'=>'require',
        'openid'=>'require',
        'union_id'=>'require',
        'nickname'=>'require',
    ];
    protected $message = [
        "telephone.require" => "请填写手机号",
        "code.require" => "请填写验证码",
        'user_id.require'  => '用户id不可为空',
        'feedback_cate_id.require'  => '请选择反馈类型',
        'feedback_content.require'  => '请填写反馈内容',
        'password.require'=>'请填写密码',
        'openid.require'=>'缺少参数',
        'union_id.require'=>'缺少参数',
        'nickname.require'=>'缺少参数',
    ];
    protected $scene = [
        "checkCode" => "telephone,code",
    ];
}