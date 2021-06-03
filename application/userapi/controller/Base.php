<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/12/7
 * Time: 10:51
 */

namespace app\userapi\controller;

class Base extends PublicController
{

    public function _initialize()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId");
        //当前方法
        $action = request()->module() . "/" . request()->controller() . "/" . request()->action();
        //不验证签名的方法
        $ext_sign = [
            'index/Index/Index',
        ];
        if (in_array($action, $ext_sign)) {
            return;
        }
        //验证签名
        //$this->checkSign();
    }
}