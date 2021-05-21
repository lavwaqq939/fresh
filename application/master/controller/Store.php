<?php
/**
 * Created by PhpStorm.
 * User: 17828
 * Date: 2018/7/6
 * Time: 13:45
 */
namespace app\master\controller;

class Store extends Base
{
    //门店列表
    public function storeList()
    {

        return $this->fetch('storelist');
    }
}