<!DOCTYPE html>
<html>
<head>
	<title>工作流管理</title>
	<base href="<?php echo base_url(); ?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /><!--IE兼容模式使用最新的文档标准-->

    <!-- jquery -->
	<script src="common/js/jquery-1.8.2.js" type="text/javascript"></script>

	<!-- jquery UI -->
	<link href="common/css/jquery-ui-1.9.0.custom.css" rel="stylesheet" />		
	<script src="common/js/jquery-ui-1.9.0.custom.js" type="text/javascript"></script>
	<script src="common/js/jquery.ui.datepicker-zh-CN.js" type="text/javascript"></script>

	<!-- jquery plugins -->
	<script src="common/js/jquery.autocomplete.js" type="text/javascript"></script>
	<script src="common/js/jquery.form.js" type="text/javascript" ></script>

	<!-- jquery block -->
	<link href="common/plugins/css/jq.block_screen.css" rel="stylesheet" />		
	<script src="common/plugins/js/jq.block_screen.js" type="text/javascript"></script>
	
	<!-- 简易浮层提示 -->
	<script src="common/plugins/js/jq.float_hint.js"></script>
	<link href="common/plugins/css/jq.float_hint.css" rel="stylesheet">
	
	<!-- 返回顶部 -->
	<script src="common/plugins/js/jq.to_top.js"></script>
	<link href="common/plugins/css/jq.to_top.css" rel="stylesheet">

	<!-- datatable -->
	<link href="common/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet" />	
	<link href="common/plugins/datatables/css/jquery.dataTables_crm.css" rel="stylesheet" />		
	<script src="common/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript" ></script>

	<script src="common/plugins/js/datatable_plugin.js" type="text/javascript" ></script>

	<!-- bootstrap -->
	<link href="common/css/bootstrap.min.css" rel="stylesheet" />
	<link href="common/css/bootstrap-select.css" rel="stylesheet" />
	<script src="common/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="common/js/bootstrap-select.js" type="text/javascript"></script>
	<script src="common/js/ajax-bootstrap-select.js" type="text/javascript"></script>

	<!-- bootstrap 统一自定义css -->
	<link href="common/css/bootstrap_top.css" rel="stylesheet" />
	
	<!-- 	layer弹出框样式 -->
	<script type="text/javascript" src="common/plugins/layer/layer.js"></script>
	<script type="text/javascript" src="common/plugins/layer/extend/layer.ext.js"></script>

    <!-- 通用方法引用 -->
    <script type="text/javascript" src="common/js/function.js"></script>

</head>

<body>
<div class="back_title">
    <h2>
    当前用户：<?php echo isset($_SESSION['name'])?$_SESSION['name']:'未登陆';?> <a href="<?php echo site_url('login')?>">切换用户 </a>
    </h2>
</div>	 