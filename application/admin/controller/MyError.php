<?php
namespace app\admin\controller;
use app\admin\common\Base;
class MyError extends Base
{
    function _empty(){
		$this->error('页面不存在','login/login');
	}
}