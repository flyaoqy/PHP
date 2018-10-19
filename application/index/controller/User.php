<?php 
namespace app\index\controller;
use think\Controller;
class User extends Controller {

    public function index() {
        return $this->fetch('user');

    }


    public function capi() {

        // http协议请求
        $url = 'http://localhost/yz/thinkphp/index.php/index/api/api/';
        // input('sno') 是前端的from传过来的name值
        $ch = curl_init($url.'?sno='.input('sno'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // 执行 并把执行后的数据赋值给 $data
        $data = curl_exec($ch);
        // 关闭
        curl_close($ch);
        // 返回数据
        return $data;
    }

}