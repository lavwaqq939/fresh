<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/8/5
 * Time: 16:26
 */

namespace app\master\controller;

class User extends PublicController
{

    /**
     * 登陆
     */
    public function login()
    {
        $this->view->engine->layout(false);
        if (!request()->isPost()) {
            session(null);
            return $this->fetch('login');
        }
        //接受数据
        $data['username'] = input('post.username');
        $data['password'] = md5(input('post.password'));
        $data['captcha'] = input('post.captcha');
        //校验数据
        $validate = validate('admin');
        if (!$validate->scene('login')->check($data)) {
            $this->error($validate->getError());
        }
        unset($data['captcha']);
        //查询数据
        $adminModel = model('admin');
        $admin = $adminModel::get($data);

        // 判断是否有该用户
        if (!$admin) {
            $this->error('用户名或密码错误');
        }
        // 将用户信息存入缓存
        session('admin_id', $admin->admin_id);
        session('admin_name', $admin->username);

        // 为用户记录登陆时间
        $admin->last_login_time = time();
        $admin->save();

        //登录成功
        $url = urldecode(input('url'));
        if (!$url) {
            $url = url('Statistics/user');
        }
        $this->success('登录成功', $url);

    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $this->view->engine->layout(false);
        session(null);
        $this->redirect('User/login');
    }
}