<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/12/6
 * Time: 15:58
 */

namespace app\master\controller;

use think\captcha\Captcha;
use think\Controller;
use think\exception\HttpResponseException;
use think\Response;

class PublicController extends Controller
{

    public function rbac(){
        return new Auth(config('rbac'));
    }


    /**
     * 检测图形验证码
     * @param $code
     * @return bool
     */
    public function checkCaptcha($code)
    {
        $captcha = new Captcha();
        return $captcha->check($code);
    }

    /**
     * layui table 表格返回方法
     * @param int $code
     * @param string $msg
     * @param int $count
     * @param array $data
     */
    public function _table_return($code = 0, $msg = "", $count = 0, $data = [])
    {
        return $this->_return(['code' => $code, 'msg' => $msg, 'count' => $count, 'data' => $data]);
    }

    /**
     * ajax 返回方法
     * @param int $code
     * @param string $msg
     * @param string $url
     * @param array $data
     */
    public function _ajax_return($code = 0, $msg = "", $url = '', $data = [])
    {
        return $this->_return(['code' => $code, 'msg' => $msg, 'jumpUrl' => $url, 'data' => $data]);
    }

    /**
     * 返回
     */
    public function _return($response = [], $header = [])
    {

        $response = Response::create($response, 'json')->header($header);
        throw new HttpResponseException($response);
    }
}