<?php
namespace app\index\controller;
use think\Log;
use think\Controller;
use app\index\model\Member as MemberModel;
class Member extends Controller
{
    public function list()
    {
		Log::init([
        'type'  =>  'File',
        'path'  =>  APP_PATH.'logs/'
        ]);
        $list=MemberModel::with('photo,basic')->find('2')->toArray();//查询一条ID为2的用户数据；toArray()是将结果转为数组。
		echo "<pre>";
        print_r($list);	
		echo __DIR__;//当前执行的php脚本所在目录
		//Log::write('测试日志信息，这是警告级别，并且实时写入','notice',true);
    }
}