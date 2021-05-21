<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/12/6
 * Time: 15:58
 */

namespace app\userapi\controller;

use app\common\rbac\Auth;
use think\captcha\Captcha;
use think\Controller;

class PublicController extends Controller
{

    /**
     * 验证签名
     */
    public function checkSign(){
        $APP_ID = array(
            'i' => config('DATA_APP_ID_IOS'),
            'a' => config('DATA_APP_ID_AND'),
            'w' => config('DATA_APP_ID_WEB'),
        );
        $encStr = config("DATA_ENCSTR");
        //获取请求参数
        $param = request()->isPost() ? input('post.') : input('get.');
        if (empty($param['APP_ID'])) $this->_ajax_return(601,"缺少必传参数");
        if (!in_array($param['APP_ID'], $APP_ID)) {
            $this->_ajax_return(601, 'APP_ID错误！');
        }

        //排除请求参数中的sign字段
        $sign = $param['sign'];
        //销毁变量
        unset($param['sign']);

        //按键名排序请求参数
        ksort($param);
        $str = '';
        foreach ($param as $k => $v) {
            //排除二维数组
            if (is_array($v)) {
                continue;
            }
            $str .= $k . $v;
        }
//        $a = $encStr . $param['APP_ID'] . $str;
//        $this->_ajax_return(200,'sign测试',$a);
        $signResult = strtoupper(md5($encStr . $param['APP_ID'] . $str));
//        $res = [
//            'data' => $str,
//            'true_sign' => $signResult
//        ];
        if ($sign != $signResult) {
            $this->_ajax_return(602, 'sign验证错误');
        }
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
     * ajax 返回方法
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    public function _ajax_return($code = 0, $msg = "", $data = [])
    {
        $data = array(
            "code" => $code,
            "msg" => $msg,
            "data" => $data
        );
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }

    /**
     * 返回
     */
    public function _return($response = [])
    {
//        header('Content-type: application/json');
        return (json($response));
    }
}