<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/12/7
 * Time: 9:55
 */

namespace app\userapi\controller;

use app\common\validate\User;
use think\captcha\Captcha;

class Common extends PublicController
{
    /**
     * 文件上传
     */
    public function upload()
    {
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads';
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $info = $file->move($path);
            if ($info) {
                $url = request()->domain() . request()->root() . "/" . 'uploads' . "/" . $info->getSaveName();
                $this->_ajax_return(0, '', ['src' => $url]);
            } else {
                // 上传失败获取错误信息
                $this->_ajax_return(0, $file->getError());
            }
        }
    }

    /**
     * 生成验证码
     * @return \think\Response
     */
    public function captcha()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }

    /**
     * 发送验证码
     */
    public function sendCode(){
        $telephone = input("telephone");
        if(!checkTelFormat($telephone)) $this->_ajax_return(603,"手机号格式错误");
        if(!$telephone) $this->_ajax_return(603,config("PARAM_POINT_OUT"));
        $result = model("vcode")->sendCode($telephone);
        if(!$result) $this->_ajax_return(603,"请求频繁,请稍后再试");
        $this->_ajax_return(200,config("SUCCESS_POINT_OUT"));
    }

    /**
     * 验证验证码
     */
    public function checkCode(){
        $data = input("get.");
        $userValidate = new User();
        $msg = $userValidate->scene("checkCode")->check($data);
        if(!$msg) $this->_ajax_return(603,$userValidate->getError());
        $result = model("vcode")->checkCode($data["telephone"],$data["code"]);
        if(!$result["result"]) $this->_ajax_return(603,$result["msg"]);
        $this->_ajax_return(200,config("SUCCESS_POINT_OUT"));
    }
    /**
     * 地区列表
     */
    public function getAreaList(){
        $pid  = input("pid");
        $field = input("field");
        $pid = !$pid ? 0 : $pid;
        $field = !$field ? "area_id,area_name":$field;
        $where = [
            "pid"=>$pid
        ];
        $list = db("Area")->where($where)->field($field)->select();
        $this->_ajax_return(200,config("SUCCESS_POINT_OUT"),$list);
    }


}