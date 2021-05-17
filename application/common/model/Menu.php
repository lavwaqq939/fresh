<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/11/4
 * Time: 15:10
 */

namespace app\common\model;

use think\Model;

class Menu extends Model
{
    public function getMenu($where = array(), $is_order = 0)
    {
        if ($is_order) {
            $data = db('Menu')->where($where)->order('order_number desc')->select();
        } else {
            $data = db('Menu')->where($where)->select();
        }
        if ($data) {
            foreach ($data as $k => $v) {
                $data[$k]['icon'] = $v['icon'];
                $data[$k]['ricon'] = htmlspecialchars_decode($v['icon']);
            }
        }
        return $data;
    }

    /**
     * 添加菜单
     */
    public function addMenu($data)
    {
        if (false === $this->save($data)) {
            return false;
        }
        return true;
    }

    /**
     * 左菜单
     */
    public function getLeftMenu($active)
    {
        $parentMenu = collection($this->getMenu(['pid' => '0', 'status' => 1], 1))->toArray();
        $subMenu = collection($this->getMenu(['pid' => ['neq', '0'], 'status' => 1], 1))->toArray();
        foreach ($parentMenu as $key => $val) {
            $parentMenu[$key]['p_active'] = 0;
            foreach ($subMenu as $v) {
                if ($v['pid'] == $val['menu_id']) {
                    $v['active'] = 0;
                    if ($v['rule'] == $active) {
                        $v['active'] = 1;
                        $parentMenu[$key]['p_active'] = 1;
                    }
                    $parentMenu[$key]['sub'][] = $v;

                }
            }
            $parentMenu[$key]['active'] = 0;
            if ($val['rule'] == $active) {
                $parentMenu[$key]['active'] = 1;
            }
        }

        return $parentMenu;
    }
}