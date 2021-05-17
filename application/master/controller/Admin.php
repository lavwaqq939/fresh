<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/11/13
 * Time: 23:45
 */

namespace app\master\controller;
class Admin extends Base
{
    public function editPassword()
    {
        if (!request()->isPost())
            return $this->fetch('edit_password');
        $password = input('password');
        $com_password = input('compassword');
        if ($password != $com_password) {
            return $this->_ajax_return(603, '两次密码不一致');
        }
        model('admin')->save(['password' => md5($password)], ['admin_id' => session('admin_id')]);
        return $this->_ajax_return(200, '修改成功', url('user/logout'));
    }
}