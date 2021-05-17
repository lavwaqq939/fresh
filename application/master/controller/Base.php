<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/12/7
 * Time: 10:51
 */

namespace app\master\controller;

class Base extends PublicController
{
    public function _initialize()
    {
        //当前方法
        $action = request()->module() . "/" . request()->controller() . "/" . request()->action();
        //登陆不判断登陆状态
        $ext_login = [
            'index/User/login'
        ];
        if (in_array($action, $ext_login)) {
            return;
        }
        //判断登陆状态
        if (!$this->checkLogin()) {
            $url = request()->url();
            $this->error('请登陆系统', url('user/login') . "?url=" . urlencode($url));
        }


        //向所有页面中传入当前页面的使用者和当前页面的方法
        $this->assign('username', session('admin_name'));
        $this->assign('action', $action);

        //获取顶部菜单
        $this->assign('top_menu', '');
        //获取左侧菜单
        $left_menu = model('Menu')->getLeftMenu($action);
        $this->assign('left_menu', $left_menu);
    }

    /**
     * 检测登陆状态
     * @return bool
     */
    public function checkLogin()
    {
        if (session('admin_id')) {
            return true;
        };
        return false;
    }
}