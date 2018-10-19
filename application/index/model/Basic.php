<?php
namespace app\index\model;
use think\Model;
class Basic extends Model
{
   public function member()
    {
        return $this->belongsTo('Member');
    }
}
