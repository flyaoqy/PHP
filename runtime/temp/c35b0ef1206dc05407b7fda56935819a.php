<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"D:\wamp64\www\yz\ThinkPHP./application/admin\view\order\order_list.html";i:1532509086;}*/ ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/yz/thinkphp/public/static/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/font.css">
    <link rel="stylesheet" href="/yz/thinkphp/public/static/admin/css/xadmin.css">
	<link rel="stylesheet" href="/yz/thinkphp/public/static/admin/lib/layui/css/layui.css">
	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
        <a href="">合同管理</a>
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace('index')" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="x-body">
      <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" type="POST" action="<?php echo url('search'); ?>">
          <div class="layui-input-inline">
            <select name="status">
              <option value="">合同状态</option>
              <option value="0">未审核</option>
              <option value="1">已审核</option>
			  <option value="2">已解析</option>
            </select>
          </div>
          <div class="layui-input-inline">
            <select name="payment_method">
              <option value="">结账方式</option>
              <option value="月度结算">月度结算</option>
              <option value="季度结算">季度结算</option>
              <option value="半年结算">半年结算</option>
			  <option value="整年结算">整年结算</option>
            </select>
          </div>
          <div class="layui-input-inline">
            <select name="retail_format">
              <option value="">业态名称</option>
              <option value="主次力店">主次力店</option>
              <option value="精品配套">精品配套</option>
              <option value="服装服饰">服装服饰</option>
              <option value="餐饮美食">餐饮美食</option>
              <option value="休闲娱乐">休闲娱乐</option>
            </select>
          </div>
          <input type="text" id="company_name" name="company_name"  placeholder="请输入商户名称" autocomplete="off" class="layui-input">
          <button class="layui-btn"  lay-submit="" lay-filter="sreach" type="submit"><i class="layui-icon">&#xe615;</i></button>
        </form>
      </div>
      <xblock>
        <button class="layui-btn" onclick=x_admin_show('添加合同','<?php echo url("create"); ?>',990,460)><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:40px">共有数据：<?php echo $count; ?> 条</span>
      </xblock>
	  <div class="table-container">
      <table class="layui-table" lay-data="" lay-size="sm">
        <thead>
          <tr>
			<th lay-data="{field:'id',width:50,sort: true}">ID</th>
            <th lay-data="{field:'contract_id',width:90,sort: true}">合同编号</th>
            <th lay-data="{field:'type',width:90}">合同类型</th>
            <th lay-data="{field:'name',width:180}">合同名称</th>
            <th lay-data="{field:'company_name',width:220}">商户名称</th>
            <th lay-data="{field:'code',width:90,sort: true}">商户代码</th>
            <th lay-data="{field:'belonger',width:90,sort: true}">所属人员</th>
            <th lay-data="{field:'brand',width:110}">品牌</th>
			<th lay-data="{field:'retail_format',width:90,sort:true}">业态名称</th>
            <th lay-data="{field:'berth_number',width:80,sort:true}">铺位号</th>
            <th lay-data="{field:'payment_method',width:85,sort:true}">结账方式</th>
            <th lay-data="{field:'startdate',width:110,sort:true}">合同开始时间</th>
            <th lay-data="{field:'enddate',width:110,sort:true}">合同结束时间</th>
			<th lay-data="{field:'upload',width:85,sort:true}">合同附件</th>
			<th lay-data="{field:'status',width:80,sort:true}">状态</th>
			<th lay-data="{field:'operate',width:70}">操作</th>
            </tr>
        </thead>
        <tbody>
		<?php if(is_array($store_contract) || $store_contract instanceof \think\Collection || $store_contract instanceof \think\Paginator): $i = 0; $__LIST__ = $store_contract;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
          <tr>
            <td><?php echo $vo['id']; ?></td>
			<td><?php echo $vo['contract_id']; ?></td>
            <td><?php echo $vo['type']; ?></td>
            <td><?php echo $vo['name']; ?></td>
            <td><?php echo $vo['company_name']; ?></td>
            <td><?php echo $vo['code']; ?></td>
            <td><?php echo $vo['belonger']; ?></td>
            <td><?php echo $vo['brand']; ?></td>
			<td><?php echo $vo['retail_format']; ?></td>
            <td><?php echo $vo['berth_number']; ?></td>
            <td><?php echo $vo['payment_method']; ?></td>
            <td><?php echo $vo['startdate']; ?></td>
			<td><?php echo $vo['enddate']; ?></td>
			<td>
			    <a href="http://192.168.2.6/yz/thinkphp/public/uploads/<?php echo $vo['upload']; ?>" download="<?php echo $vo['upload']; ?>" width="150" alt="">下载附件</a>
			</td>
			<td class="td-status">
              <?php if($vo['status']==0): ?>
              <span id="aa" class="layui-btn layui-btn-normal layui-btn-mini">未审核</span>
			  <?php endif; if($vo['status']==1): ?>
			   <span id="ab" class="layui-btn layui-btn-mini">已审核</span>
			  <?php endif; if($vo['status']==2): ?>
			   <span id="ac" class="layui-btn layui-btn-disabled layui-btn-mini">已解析</span>
			  <?php endif; ?>
			</td>
            <td class="td-manage">
              <a title="查看合同" target=_blank href="http://192.168.2.6/yz/thinkphp/public/uploads/<?php echo $vo['upload']; ?>">
                <i class="layui-icon">&#xe63c;</i>
              </a>			  
			  <a title="编辑"  onclick=x_admin_show('编辑','<?php echo url("edit"); ?>'+'?id='+'<?php echo $vo['id']; ?>',990,460) href="javascript:;">
                <i class="layui-icon">&#xe642;</i>
              </a>
			  <!-- -->
              <!--<a title="删除" onclick="member_del(this,'<?php echo $vo['id']; ?>')" href="javascript:;">
                <i class="layui-icon">&#xe640;</i>
              </a>-->
            </td>
          </tr>
		<?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
       </table>
        <div style="padding-top:10px ">
	      <?php echo $store_contract->render(); ?>
	    </div>
    </div>
    <script>
     
	  layui.use('table', function(){
        var table = layui.table;
        });
		
      layui.use('laydate', function(){
        var laydate = layui.laydate;
        
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
          layer.confirm('确认要停用吗？',function(index){

              if($(obj).attr('title')=='启用'){

                //发异步把用户状态进行更改
                $(obj).attr('title','停用')
                $(obj).find('i').html('&#xe62f;');

                $(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-disabled').html('已停用');
                layer.msg('已停用!',{icon: 5,time:1000});

              }else{
                $(obj).attr('title','启用')
                $(obj).find('i').html('&#xe601;');

                $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                layer.msg('已启用!',{icon: 5,time:1000});
              }
              
          });
      }

      /*用户-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除吗？',function(index){
              //发异步删除数据
			  $.get("<?php echo url('delete'); ?>",{id:id});
              $(obj).parents("tr").remove();
              layer.msg('已删除!',{icon:1,time:1000});
          });
      }
      function delAll (argument) {

        var data = tableCheck.getData();
  
        layer.confirm('确认要删除吗？'+data,function(index){
            //捉到所有被选中的，发异步进行删除
            layer.msg('删除成功', {icon: 1});
            $(".layui-form-checked").not('.header').parents('tr').remove();
        });
      }
    </script>
    </div>>
  </body>
</html>