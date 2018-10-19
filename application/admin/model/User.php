<?php

namespace app\admin\model;
use think\Model;
class User extends Model
{
	public function Role()
    {
        return $this->hasMany('Role','id');
    }	
}
