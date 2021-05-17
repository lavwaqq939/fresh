<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/11/4
 * Time: 14:28
 */

namespace app\master\controller;

class Menu extends Base
{
    /**
     * 菜单页面
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取菜单
     */
    public function getMenu()
    {
        //关闭layout布局
        $this->view->engine->layout(false);
        $data = model('Menu')->getMenu();

        return $this->_table_return(0, '查询成功', 0, $data);
    }

    /**
     * 修改菜单
     */
    public function editMenu()
    {
        $field = input('post.field');
        $value = input('post.value');
        $id = input('post.id');
        if (false === model('Menu')->save([$field => $value], ['menu_id' => $id])) {
            return $this->_ajax_return(603, '修改失败');
        }
        return $this->_ajax_return(200, '修改成功');
    }

    /**
     * 添加菜单
     */
    public function addMenu()
    {
        $this->view->engine->layout(false);
        if (!request()->isPost()) {
            $id = intval(input('id'));
            $this->assign('id', $id);
            return $this->fetch('addmenu');
        }
        $data['pid'] = intval(input('post.id'));
        $data['title'] = input('post.title');
        $data['rule'] = input('post.rule');
        $data['icon'] = input('post.icon');
        $data['order_number'] = input('post.order_number');
        $data['status'] = input('post.status');

        if (false === model('Menu')->addMenu($data)) {
            return $this->_ajax_return(603, '添加失败');
        };
        return $this->_ajax_return(200, '添加成功');
    }

}