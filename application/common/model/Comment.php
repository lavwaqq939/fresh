<?php
/**
 * Created by PhpStorm.
 * User: 17828
 * Date: 2018/7/7
 * Time: 16:55
 */
namespace app\common\model;

use think\Model;

class Comment extends Model
{
    public function getCommentList($field,$where,$page)
    {
        $comment = db('comment')->alias('c')
            ->field($field)
            ->join('user u','u.user_id = c.user_id')
            ->where($where)
            ->order('c.comment_id desc')
            ->page($page,config("page"))
            ->select();
        return $comment;
    }
}