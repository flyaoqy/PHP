<?php 
namespace app\index\controller;
use think\Controller;
use think\db;

class Api
{

    public function index() {
        return $this->fetch('');
    }


    // 客户端提交学生学号（sno）给api    api返回此学生的基本信息

    public function api($sno) {
        // 查询 并把数据赋值给 $data
        $data = db('user')->where('id',$sno)->find();
        // 返回数据
        return json($data);
    }

}