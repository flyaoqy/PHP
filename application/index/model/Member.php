<?php
namespace app\index\model;
use think\Model;
class Member extends Model
{
    public function basic()
    {
        return $this->hasOne('Basic','uid','id');//hasOne��һ��һ
    }
    //������photo�Ĺ���
    public function photo()
    {
        return $this->hasMany('photo','uid','id');//hasMany��һ�Զ�
    }
}
