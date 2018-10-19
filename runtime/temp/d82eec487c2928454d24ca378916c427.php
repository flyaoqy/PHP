<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"D:\wamp64\www\yz\ThinkPHP./application/admin\view\member\member_list.html";i:1532508501;}*/ ?>
<!DOCTYPE html>
<html>
  
  <head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.0</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/yz/thinkphp/public/static/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/font.css">
    <link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/xadmin.css">
	<link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css"> 
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="/yz/thinkphp/public/static/admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/yz/thinkphp/public/static/admin/js/xadmin.js"></script>
	<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
      <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
      <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <script>
      let index = parent.layer.getFrameIndex(window.name);
      parent.layer.close(index);
      parent.location.reload();
  </script>
  <body>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="">首页</a>
        <a href="javascript:location.replace('index')">商户管理</a>
        <!--<a><cite>商户列表</cite></a>-->
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace('index')" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a> <!--注意location.replace('index')写法-->
    </div>
    <div class="x-body">
      <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" type="POST" action="<?php echo url('search'); ?>">
          <!--<input class="layui-input" placeholder="开始日" name="start" id="start">-->
          <!--<input class="layui-input" placeholder="截止日" name="end" id="end">-->
          <input type="text" id="company_name" name="company_name"  placeholder="请输入商户名称" autocomplete="off" class="layui-input">
          <button class="layui-btn"  lay-submit="" lay-filter="sreach" id="thesearch" type="submit"><i class="layui-icon">&#xe615;</i></button>
        </form>
      </div>
      <xblock>
        <button class="layui-btn" onclick=x_admin_show('商户添加','<?php echo url("create"); ?>',990,460)><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:40px">共有数据：<?php echo $count; ?> 条</span>
      </xblock>
	  </div>  
	  <div class="table-container">
      <table class="layui-table" lay-data="" lay-size="sm"> <!--lay-size -->
        <thead>
          <tr>
            <th lay-data="{field:'id',width:50,sort: true}">ID</th>
            <th lay-data="{field:'code',width:90,sort: true}">商户代码</th>
            <th lay-data="{field:'company_name',width:290}">商户名称</th>
            <th lay-data="{field:'area',width:110}">地区</th>
            <th lay-data="{field:'invoice_type',width:80}">发票种类</th>
            <th lay-data="{field:'enterprise_type',width:110,sort:true}">企业类型</th>
            <th lay-data="{field:'artificial_person',width:80}">企业法人</th>
            <th lay-data="{field:'credit_code',width:170}">统一社会信用代码</th>
            <th lay-data="{field:'registered_address',width:280}">注册地址</th>
			<th lay-data="{field:'contacts',width:80}">联系人</th>
			<th lay-data="{field:'office_address',width:280}">办公地址</th>
			<th lay-data="{field:'telno1',width:110}">电话一</th>
			<th lay-data="{field:'telno2',width:110}">电话二</th>
			<th lay-data="{field:'brand',width:80}">品牌</th>
			<th lay-data="{field:'cheque',width:200}">发票抬头</th>
			<th lay-data="{field:'taxable_type',width:100,sort:true}">纳税类型</th>
			<th lay-data="{field:'tax_rate',width:40,sort: true}">税率</th>
			<th lay-data="{field:'status',width:80}">状态</th>
            <th lay-data="{field:'operate',width:100}">操作</th></tr>
        </thead>
        <tbody>
		<?php if(is_array($member) || $member instanceof \think\Collection || $member instanceof \think\Paginator): $i = 0; $__LIST__ = $member;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?> <!--name属性表示模板赋值的变量名称，因此不可随意在模板文件中改变。
		id表示当前的循环变量，可以随意指定，但确保不要和name属性冲突-->
          <tr>
            <td><?php echo $vo['id']; ?></td>
            <td><?php echo $vo['code']; ?></td>
            <td><?php echo $vo['company_name']; ?></td>
            <td><?php echo $vo['area']; ?></td>
            <td><?php echo $vo['invoice_type']; ?></td>
            <td><?php echo $vo['enterprise_type']; ?></td>
            <td><?php echo $vo['artificial_person']; ?></td>
			<td><?php echo $vo['credit_code']; ?></td>
			<td><?php echo $vo['registered_address']; ?></td>
			<td><?php echo $vo['contacts']; ?></td>
			<td><?php echo $vo['office_address']; ?></td>
			<td><?php echo $vo['telno1']; ?></td>
			<td><?php echo $vo['telno2']; ?></td>
			<td><?php echo $vo['brand']; ?></td>
			<td><?php echo $vo['cheque']; ?></td>
			<td><?php echo $vo['taxable_type']; ?></td>
			<td><?php echo $vo['tax_rate']; ?></td>
            <td class="td-status">
              <?php if($vo['status']==0): ?>
              <span id="aa" class="layui-btn layui-btn-mini">已启用</span>
			  <?php endif; if($vo['status']==1): ?>
			   <span id="ab" class="layui-btn layui-btn-disabled layui-btn-mini">已停用</span>
			  <?php endif; ?>
			</td>
            <td class="td-manage">
			  <?php if($vo['status']==0): ?>
              <a onclick="member_stop(this,'<?php echo $vo['id']; ?>')" href="javascript:"  title="停用">
                <i class="layui-icon">&#xe601;</i>
              </a>
			  <?php endif; if($vo['status']==1): ?>
              <a onclick="member_stop(this,'<?php echo $vo['id']; ?>')" href="javascript:"  title="启用">
                <i class="layui-icon">&#xe601;</i>
              </a>
			  <?php endif; ?>
              <a title="编辑"  onclick=x_admin_show('商户修改','<?php echo url("edit"); ?>'+'?id='+'<?php echo $vo['id']; ?>',960,460) href="javascript:;">
                <i class="layui-icon">&#xe642;</i>
              </a>
              <a title="删除" onclick="member_del(this,'<?php echo $vo['id']; ?>')" href="javascript:">
                <i class="layui-icon">&#xe640;</i>
              </a>
            </td>
          </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
      </table>
	    <div style="padding-top:10px ">
	      <?php echo $member->render(); ?>
	    </div>
	</div>	
    <script>
      layui.use('table', function(){
        let table = layui.table;
        });
	   
      layui.use('laydate', function(){
        let laydate = layui.laydate;
        
        //执行一个laydate实例
        laydate.render({
          elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
          elem: '#end' //指定元素
        });
      });

       /*用户-停用*/
      function member_stop(obj,id){
          layer.confirm('确认吗？',function(){
              if($(obj).attr('title')=='停用'){
                //发异步把用户状态进行更改
				$.get("<?php echo url('stop'); ?>",{id:id});
                $(obj).attr('title','停用');
                $(obj).find('i').html('&#xe62f;');
                $(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-disabled').html('已停用');
                layer.msg('已停用!',{icon: 5,time:1000},function () {
                    location.replace(location.href);
                });

              }else{
                $(obj).attr('title','启用');
				$.get("<?php echo url('start'); ?>",{id:id});
                $(obj).find('i').html('&#xe601;');
                $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                layer.msg('已启用!',{icon: 1,time:1000},function () {
                    location.replace(location.href);
                });
              }

          });
      }
      /*用户-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除吗？',function(){
              //发异步删除数据
			  $.get("<?php echo url('delete'); ?>",{id:id});
              $(obj).parents("tr").remove();//什么意思？
              layer.msg('已删除!',{icon:1,time:1000});//icon为图标的显示样式，取值有1、2、3、4、5、6、7
          });
      }
    </script>
  </body>
</html>