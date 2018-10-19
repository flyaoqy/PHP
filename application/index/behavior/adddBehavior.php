<?php
namespace app\index\behavior;//注意应用或模块的不同命名空间
/**
 * 行为扩展:广告插件test.
 * author lzq(674531003@qq.com)
 */
class adddBehavior {
    public function run(&$params) {
       echo '我是一条'.$params['name'].'广告,'.$params['value'].'的值'.$params['address'];
    }
}