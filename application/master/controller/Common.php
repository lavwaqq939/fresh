<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/12/7
 * Time: 9:55
 */

namespace app\master\controller;

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
                return $this->_ajax_return(0, '', '', ['src' => $url]);
            } else {
                // 上传失败获取错误信息
                return $this->_ajax_return(0, $file->getError());
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

}