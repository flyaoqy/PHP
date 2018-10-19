<!DOCTYPE html>
<html lang="en">
  <head>
    <title>PHP工作流引擎</title>
	<base href="<?php echo base_url(); ?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /><!--IE兼容模式使用最新的文档标准-->
	<!-- jquery -->
	<script src="common/js/jquery-1.8.2.js" type="text/javascript"></script>
	
    <!-- Bootstrap core CSS -->
    <link href="common/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="common/css/dashboard.css" rel="stylesheet">

  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="<?php echo HOME_BASE_URL?>">PHP工作流引擎</a>
        </div>
        <div class="navbar-collapse collapse">
        	<ul class="nav navbar-nav navbar-left">
        		<li <?php echo $sub_title=="home"?'class="active"':''; ?>><a href="<?php echo HOME_BASE_URL?>">首页</a></li>
				<li <?php echo $sub_title=="doc"?'class="active"':''; ?>><a href="<?php echo site_url()?>/index?sub_title=doc">文档</a></li>
				<li <?php echo $sub_title=="example"?'class="active"':''; ?>><a href="<?php echo site_url()?>/index?sub_title=example">示例</a></li>
				<li <?php echo $sub_title=="manage"?'class="active"':''; ?>><a href="<?php echo site_url()?>/index?sub_title=manage">管理工具</a></li>
				<li <?php echo $sub_title=="download"?'class="active"':''; ?>><a href="<?php echo HOME_BASE_URL?>/index.php/index?sub_title=download">下载</a></li>
			</ul>
        </div>
      </div>
    </div>
<script type="text/javascript">
	$(function(){
		
		$('div[left_area]').on('click','li',function(){
			var element = $(this);
			var url = element.attr('url');
			element.addClass('active');
			element.siblings().removeClass('active');
			$('#main_frame').attr('src',url);
		});
		$('div[left_area]').find('li').first().click();
	});
</script>