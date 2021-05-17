<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/8/7
 * Time: 14:14
 */


namespace app\common\validate;

use think\Validate;

class Admin extends Validate
{
    //验证规则
    protected $rule = [
        ['username', 'require', '请输入用户名'],
        ['password', 'require', '请输入密码'],
        ['captcha', 'require|captcha']
    ];

    //场景
    protected $scene = [
        'login' => ['username.require', 'password.require', 'captcha'], //登陆 验证数据存在
    ];

}