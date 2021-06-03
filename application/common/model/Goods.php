<?php
/**
 * Created by PhpStorm.
 * User: 17828
 * Date: 2018/7/7
 * Time: 14:08
 */
namespace app\common\model;
use think\Model;

class Goods extends Model
{
    public function getGoodsInfo($field,$where)
    {
        $goods = db('goods')->alias('g')
            ->field($field)
            ->join('store_goods s', 's.goods_id = g.goods_id')
            ->join('category c','c.category_id = g.category_id')
            ->where($where)
            ->find();
        return $goods;
    }
}