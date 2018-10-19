<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
		\think\Hook::add('addd','app\\index\\behavior\\adddBehavior');
        echo ($this->fetch('index'));

    }
}