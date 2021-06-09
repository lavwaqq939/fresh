<?php
/**
 * Created by PhpStorm.
 * User: 17828
 * Date: 2018/7/6
 * Time: 16:33
 */
namespace app\common\model;
use think\Model;

class Store extends Model
{
    //门店列表
    public function queryRange($longitude,$latitude,$page,$user_id,$store_id=null)
    {
        $where = ['status'=>1];
        if(isset($store_id)) {
            $where['store_id'] = ['in',$store_id];
        }
        $field = "store_id,store_name,store_img,dispatch_start,dispatch_end,score,longitude,latitude";
        $store = db('store')->field($field)->where($where)->page($page,config("page"))->select();
        foreach ($store as $k => $v) {
            $destination = $store[$k]['longitude'].','.$store[$k]['latitude'];
            $origins = $longitude.','.$latitude;
            $app = "http://restapi.amap.com/v3/distance?origins=$origins&destination=$destination&output=JSON&type=0&key=d76a846aa32a0dfd9a58e788b16bdc74";
            $Service = file_get_contents($app);
            $Service = json_decode($Service, true);
            if($Service['status']!=1) return false;
            foreach ($Service['results'] as $key=>$val){
                $store[$k]['distance'] = $val['distance'];
            }
            $collect = db('user_collect')->where(['user_id'=>$user_id,'store_id'=>$store[$k]['store_id']])->find();
            if($collect) $store[$k]['is_collect'] = 1;//收藏
            else $store[$k]['is_collect'] = 2;
        }
        return $store;
    }
    public function getStoreCate($field,$where)
    {
        $cate = db('goods')->alias('g')
            ->field($field)
            ->join('store_goods s', 's.goods_id = g.goods_id')
            ->join('category c','c.category_id = g.category_id')
            ->order('category_id desc')
            ->where($where)
            ->select();
        return $cate;
    }
    public function getStoreGoods($field,$where,$page)
    {
        $goods = db('goods')->alias('g')
            ->field($field)
            ->join('store_goods s', 's.goods_id = g.goods_id')
            ->join('category c','c.category_id = g.category_id')
            ->where($where)
            ->order('goods_id desc')
            ->page($page,config("page"))
            ->select();
        return $goods;
    }
    //返回门店信息
    public function getStoreInfo($field,$where)
    {
        $store = db('store')->field($field)->where($where)->find();
        return $store;
    }
}