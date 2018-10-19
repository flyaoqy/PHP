<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"D:\wamp64\www\yz\ThinkPHP./application/admin\view\login\login.html";i:1523604363;}*/ ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>商户管理系统</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/font.css">
	<link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/xadmin.css">
    <!--<script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script> -->
    <script type="text/javascript" src="/yz/thinkphp/public/static/admin/js/jquery-3.3.1.min.js"></script>
    <script src="/yz/thinkphp/public/static/admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/yz/thinkphp/public/static/admin/js/xadmin.js"></script>
</head>
<body class="login-bg">
    
    <div class="login">
        <div class="message">后台管理登录</div>
        <div id="darkbannerwrap"></div>
        <!--action跳转需要注意写法****** -->
        <form method="post" class="layui-form" method="POST" action="<?php echo url('check'); ?>"> 
            <input id="username" name="username" placeholder="用户名"  type="text" lay-verify="required" class="layui-input" >
            <hr class="hr15">
            <input id="password" name="password" lay-verify="required" placeholder="密码"  type="password" class="layui-input">
            <hr class="hr15">
            <input value="登录" lay-submit="" lay-filter="save" style="width:100%;" type="submit" id='loginbtn'>
            <hr class="hr20" >
        </form>
    </div>
    <!-- 底部结束 -->
</body>
</html>