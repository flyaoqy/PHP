<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/
//判断是否登录
$hook['post_controller_constructor'][] = array(
    'class'    => 'is_login',
    'function' => 'check',
    'filename' => 'is_login.php',
    'filepath' => 'hooks'
);
/* End of file hooks.php */
/* Location: ./application/config/hooks.php */