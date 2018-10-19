<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"D:\wamp64\www\yz\ThinkPHP./application/admin\view\index\index.html";i:1533611902;}*/ ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>后台登录</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />

    <link rel="shortcut icon" href="/favicon.ico" type="/yz/thinkphp/public/static/admin/image/x-icon" />
    <link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/font.css">
	<link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/xadmin.css">
	<link rel="stylesheet" href="/yz/thinkphp/public/static/admin/lib/layui/css/layui.css">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="/yz/thinkphp/public/static/admin/js/xadmin.js"></script>
    <script type="text/javascript" src="/yz/thinkphp/public/static/admin/lib/layui/layui.js" charset="utf-8"></script>
</head>
<body>
    <!-- 顶部开始 -->
    <div class="container">
        <div class="logo"><a href="<?php echo url('login/login'); ?>">商户管理系统</a></div>
        <div class="left_open">
            <i title="展开左侧栏" class="iconfont">&#xe699;</i>
        </div>
        <ul class="layui-nav right" lay-filter="">
            <li class="layui-nav-item layui-anim-scale layui-anim-rotate"><a><?php echo \think\Session::get('low'); ?><?php echo \think\Session::get('high'); ?></a></li>
            <li class="layui-nav-item layui-anim-scale layui-anim-rotate"><a><?php echo \think\Session::get('date'); ?></a></li>
            <li class="layui-nav-item">
                <a href="javascript:;">用户名：<?php echo \think\Session::get('user_id'); ?></a>
                <dl class="layui-nav-child"> <!-- 二级菜单 -->
                    <dd><a onclick=x_admin_show('个人信息','<?php echo url("modify"); ?>',600,320) href="javascript:;">修改密码</a></dd>
                    <dd><a href="<?php echo url('login/logout'); ?>">退出</a></dd>
              </dl>
            </li>
        </ul>
    </div>
    <!-- 顶部结束 -->
    <!-- 中部开始 -->
     <!-- 左侧菜单开始 -->
    <div class="left-nav">
      <div id="side-nav">
        <ul id="nav">
            <li>
                <a href="javascript:">
                    <i class="iconfont">&#xe6b8;</i>
                    <cite>商户管理</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('member/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>商户信息</cite>

                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:">
                    <i class="iconfont">&#xe723;</i>
                    <cite>商铺管理</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('store/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>商铺信息</cite>
                        </a>
                    </li>
                </ul>
            </li>
			<li>
                <a href="javascript:">
                    <i class="iconfont">&#xe705;</i>
                    <cite>合同管理</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('order/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>基础信息</cite>
                        </a>
                    </li>
					<li>
                        <a _href="<?php echo url('protocol/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>协议信息</cite>
                        </a>
                    </li>
					<li>
                        <a _href="<?php echo url('process/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>合同审核</cite>
                        </a>
                    </li >
                    <?php if(\think\Session::get('user_id')!=='admin'): ?>
					<li hidden>
                        <a _href="<?php echo url('process/analyze'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>数据解析</cite>
                        </a>
                    </li >
                    <?php endif; if(\think\Session::get('user_id')=='admin'): ?>
                    <li>
                        <a _href="<?php echo url('process/analyze'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>数据解析</cite>
                        </a>
                    </li >
                    <?php endif; ?>
                </ul>
            </li>
			
			<li>
                <a href="javascript:">
                    <i class="layui-icon">&#xe659;</i>
                    <cite>财务管理</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('finance/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>固定租金管理</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="<?php echo url('finance/show'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>租户费用录入</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="<?php echo url('finance/check'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>租户对账查询</cite>
                        </a>
                    </li>
                </ul>
            </li>
			
			<li>
                <a href="javascript:">
                    <i class="layui-icon">&#xe629;</i>
                    <cite>报表管理</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('report/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>应收已收统计表</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>收费汇总期报表</cite>
                        </a>
                    </li>
                </ul>
            </li>
            <?php if(\think\Session::get('user_id')!=='admin'): ?>
            <li id="li_admin" hidden>
                <a href="javascript:">
                    <i class="iconfont">&#xe726;</i>
                    <cite>系统管理</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('admin/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>用户列表</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="<?php echo url('admin/role'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>角色管理</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="<?php echo url('admin/rule'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>权限管理</cite>
                        </a>
                    </li >
                </ul>
            </li>
            <?php endif; if(\think\Session::get('user_id')=='admin'): ?>
            <li id="">
                <a href="javascript:">
                    <i class="iconfont">&#xe726;</i>
                    <cite>系统管理</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('admin/index'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>用户列表</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="<?php echo url('admin/role'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>角色管理</cite>
                        </a>
                    </li >
                    <!--
                    <li>
                        <a _href="<?php echo url('admin/cate'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>权限分类</cite>
                        </a>
                    </li >
                    -->
                    <li>
                        <a _href="<?php echo url('admin/rule'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>权限管理</cite>
                        </a>
                    </li >
                </ul>
            </li>
            <?php endif; ?>
            <li>
                <a href="javascript:">
                    <i class="iconfont">&#xe6ce;</i>
                    <cite>系统统计</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="<?php echo url('echarts/linechart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>拆线图</cite>
                        </a>
                    </li >
                    <!--
                    <li>
                        <a _href="<?php echo url('echarts/columnchart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>柱状图</cite>
                        </a>
                    </li>
                    -->
                    <li>
                        <a _href="<?php echo url('echarts/mapchart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>地图</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="<?php echo url('echarts/piechart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>饼图</cite>
                        </a>
                    </li>
                    <!--
                    <li>
                        <a _href="<?php echo url('echarts/radarchart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>雷达图</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="<?php echo url('echarts/klinechart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>k线图</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="<?php echo url('echarts/thermodynamicchart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>热力图</cite>
                        </a>
                    </li>
                    -->
                    <li>
                        <a _href="<?php echo url('echarts/meterchart'); ?>">
                            <i class="iconfont">&#xe6a7;</i>
                            <cite>仪表图</cite>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
      </div>
    </div>
    <!-- <div class="x-slide_left"></div> -->
    <!-- 左侧菜单结束 -->
    <!-- 右侧主体开始 -->
    <div class="page-content">
        <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
          <ul class="layui-tab-title">
            <li>我的桌面</li>
          </ul>
          <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe src="<?php echo url('index/welcome'); ?>" frameborder="0" scrolling="yes" class="x-iframe"></iframe>
            </div>
          </div>
        </div>
    </div>
    <div class="page-content-bg"></div>
    <!-- 右侧主体结束 -->
    <!-- 中部结束 -->
    <!-- 底部开始 -->
    <div class="footer">
        <div class="copyright">Copyright ©2018  All Rights Reserved</div>  
    </div>
    <!-- 底部结束 -->
</body>
</html>