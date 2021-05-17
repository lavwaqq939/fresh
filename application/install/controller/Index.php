<?php
/**
 * Created by PhpStorm.
 * User: lg
 * Date: 2017/12/7
 * Time: 11:34
 */

namespace app\install\controller;

use think\Db;

define('__PREFIX__', config('database.prefix'));

class Index extends PublicController
{
    public function init()
    {
        //创建菜单表
        Db::execute('CREATE TABLE `' . __PREFIX__ . 'menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT \'父栏目id\',
  `rule` varchar(100) DEFAULT NULL COMMENT \'栏目访问规则\',
  `title` varchar(45) DEFAULT NULL COMMENT \'栏目名称\',
  `icon` varchar(45) DEFAULT NULL COMMENT \'栏目图标\',
  `order_number` int(11) DEFAULT NULL COMMENT \'排序\',
  `status` int(11) DEFAULT NULL COMMENT \'是否启用\n\',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT=\'系统菜单表\';');


        //创建后台管理员表
        Db::execute('CREATE TABLE `' . __PREFIX__ . 'admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `last_login_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT=\'后台用户表\';');
        //添加后台管理员账号
        \db('admin')->insert(['username'=>'admin','password'=>md5('123456')]);
        
        //创建后台管理员表
        Db::execute('CREATE TABLE `' . __PREFIX__ . 'role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=\'角色-权限对应表\';');

    }
}