<div class="back_title">
	<h2>流程配置列表</h2>
	<button type="button"  class="btn btn-primary btn-sm" onclick="create()">新建</button>
</div>
<table class="table table-bordered">
 <tr><th>名称</th><th>操作</th></tr>
<?php

	foreach ($result as $row){
 ?>
 <tr>
    <td><?php echo $row['wf_name']  ?></td>
    <td><a href="index.php/config/workflow/wfconfig/detail?wfuid=<?php echo $row['wf_uid'] ?>" target="_blank">查看</a>
    <a href="index.php/config/workflow/wfconfig/edit?wfuid=<?php echo $row['wf_uid'] ?>" target="_blank">编辑</a>
    <a href="index.php/config/workflow/wfedit/flow?wfuid=<?php echo $row['wf_uid'] ?>" target="_blank">流程图</a>
    <a href="index.php/config/workflow/list_variate/loadView?wf_uid=<?php echo $row['wf_uid'] ?>" target="_blank">变量</a>
 	<a href="index.php/config/workflow/list_role/loadView?wf_uid=<?php echo $row['wf_uid'] ?>" target="_blank">角色</a>
 	<a href="javascript:void(0)" onclick="history_list('<?php echo $row['wf_uid'] ?>')" target="_blank">历史版本</a>
 	</td>
 </tr>

 <?php
 }
?>
</table>
<div id="history">
</div>

<div id="create_div" class="container-fluid" style="display:none" >
<form class="form-horizontal" >
	<div class="row">
		<label class="col-sm-3 col-xs-3 control-label input-sm">流程名称：</label>
		 <div class="col-sm-9 col-xs-9">
        	<input type="text"  class="form-control input-sm" name="wf_name" id="wf_name"/>
    	 </div>
	</div>

</form>
</div>
<script language="javascript">
var baseurl = "<?php echo base_url()?>";
function history_list(wfuid){
	$.ajax({
		type:"post",
		dataType:"json",
		async:"true",
		url:baseurl+'index.php/config/workflow/wfconfig/history_list',
		data:"wfuid="+wfuid, 
		success:function(data){
			var html = '<table class="table table-bordered">';
			html += '<tr><th style="text-align: center;">名称</th>';
			html += '<th style="text-align: center;">版本</th>';
			html += '<th style="text-align: center;">生成时间</th>';
			html += '<th style="text-align: center;">操作</th></tr>';
			var tmp = '';
			$.each(data, function(i, item){
	
				tmp += '<tr>';
				tmp += '<td>' + item.wf_name + '</td>';
				tmp += '<td style="text-align: center;">'+item.wf_version+'</td>';
				tmp += '<td style="text-align: center;">'+item.logtime+'</td>';
				tmp += '<td style="text-align: center;">';
				tmp += '<a href="index.php/config/workflow/wfconfig/history_detail?id='+item.id+'" target="_blank">查看</a>';
				tmp += '<a href="index.php/config/workflow/wfedit/history_flow?id='+item.id+'" target="_blank">流程图</a>';
				tmp += '</td>';
				tmp += '</tr>';
				
			});	
			if (tmp == ''){
				tmp = '<tr><td colspan="100%" style="text-align: center;">无历史版本</td></tr>';
			}
			html+= tmp;
			html+= '</table>';
			$("#history").html(html).dialog({
				title:"历史版本列表",
				resizable:false,
				width:800,
				height:400,
				modal:true,
				close: function(event, ui) {
					$(this).dialog('close');
				} ,
				buttons: {
					'关闭' : function() {
						$(this).dialog('close');
						
					}
				}
			});
		},
		error:function(){
			layer.alert("连接服务器错误！");
		}
	});	
	
}
/**
 * 新建
 */
function create(){
	
	$("#create_div").dialog({
		title:"新建",
		resizable:false,
		width:400,
		height:300,
		modal:true,
		close: function(event, ui) {
			$(this).dialog('close');
		} ,
		buttons: {
			'保存' : function() {
				$.ajax({
					type:"post",
					dataType:"json",
					async:"true",
					url:baseurl+'index.php/config/workflow/wfconfig/create',
					data:"wf_name="+$("#wf_name").val(), 
					success:function(data){
						if(data.status == 'success'){
							layer.msg("创建成功");
						 	location.reload(); 
						}else{
							layer.alert(data.msg);
						}
						
					},
					error:function(){
						layer.alert("连接服务器错误！");
					}
				});	
				
			}
		}
	});
	
}
</script>