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
    //两地距离
    public function queryRange($longitude,$latitude,$page)
    {
        $store = db('store')->where('status',1)->page($page,config("page"))->select();
        foreach ($store as $k => $v) {
            $destination = $store[$k]['longitude'].','.$store[$k]['dimensions'];
            $origins = $longitude.','.$latitude;
            $app = "http://restapi.amap.com/v3/distance?origins=$origins&destination=$destination&output=JSON&type=0&key=d76a846aa32a0dfd9a58e788b16bdc74";
            $Service = file_get_contents($app);
            $Service = json_decode($Service, true);
            if($Service['status']!=1) return false;
            foreach ($Service['results'] as $key=>$val){

            }
        }

    }
}