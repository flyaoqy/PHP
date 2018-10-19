<?php
namespace app\index\model;
use think\Model;
class Member extends Model
{
    public function basic()
    {
        return $this->hasOne('Basic','uid','id');//hasOne是一对一
    }
    //建立和photo的关联
    public function photo()
    {
        return $this->hasMany('photo','uid','id');//hasMany是一对多
    }
}
