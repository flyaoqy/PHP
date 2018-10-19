<style type="text/css">
.datalist4 th {
	text-align: center;
}

.tdcenter {
	text-align: center;
}
</style>
	<div class="container-fluid" id="datatable_container">
		<!-- 导出用form -->
		<form class="form-inline" role="form" id="theForm" name="theForm" dt_form method="POST" action="<?php echo site_url('config/workflow/list_role/export_excel');?>" >
		<input type="hidden" name="excel_orderby" />
		
		<!--表单信息-->
		<div class="search-condition" dt_search_information>
			<div class="row">
			<!-- 查询条件 -->
			<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
			<input type="text" class="form-control" name="expression" placeholder="角色名称">
			</div>
									
			</div>
			<div class="row" style="margin-top: 20px">
				<div class="col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6 col-md-6 col-md-offset-6">
				<button type="button" id="search_btn" class="btn btn-primary btn-sm">搜索</button>
				&nbsp;&nbsp;
				<button type="button"class="btn btn-primary btn-sm"  onclick="window.location.href='<?php echo site_url("config/workflow/role/open?wf_uid=".$wf_uid)?>'" >新建</button>
				</div>
			</div>				
		</form>
		<div class="search_list" style="margin-top: 20px">
		<table class="datalist4" datatable style="overflow: auto; box-sizing: border-box">
			<thead>
				<tr>
					<th></th>
											<th nowrap>id</th>
											<th nowrap>流程名称</th>
											<th nowrap>角色</th>
											<th nowrap>人员列表</th>

					<th align="center" nowrap>操作</th>
				</tr>
			</thead>
			<tbody id="content">
			</tbody>
		</table>
		</div>
		<form id="formHidden" name='formHidden' action='' method='post'>
			<input type="hidden" name="wf_uid" value="<?php echo @$wf_uid?>"/>
		</form>
</div>
<script language="javascript">

$(function() {
   	// 此方法调用在function.js，对原始方法进行了封装，可以重复定义已封装属性进行覆盖
	window.myTable = $('#datatable_container').initDataTable({
		'sAjaxSource':'<?= site_url('config/workflow/list_role/query') ?>',
		'order': [1,'desc'] ,
		'columns':[
			{
				data:'dt_primary_id',
				orderable:false,
				bVisible:false
			},
						{sName:'id',data:'id',className:'dt_nowrap'},
			{sName:'wf_name',data:'wf_name',className:'dt_nowrap'},
			{sName:'expression',data:'expression',className:'dt_nowrap'},
			{sName:'userlist',data:'userlist',className:'dt_nowrap'}
		],
		 "createdRow": function ( row, data, index ) {
		 	var id =  data['dt_primary_id'];
		 	var url = '<?php echo site_url("config/workflow/role/open?id=")?>'+id;
			var	operate = '<a href="'+url+'" >编辑</a> | <a href="javascript:void(0)" onclick="del(\''+id+'\')">删除</a>';
            $(row).append("<td>"+operate+"</td>");
	     }
	});
	
	$('#search_btn').bind('click',function(){
		window.myTable.ajax.reload();
	});
});

//删除
function del(id){

	layer.confirm("确定要删除这条数据吗？",function(){
		var url = "<?php echo site_url('config/workflow/list_role/delete');?>";
		$.ajax({     
	        url: url,    
	        type: 'post',     
	        data: 'ids=' + id,
	        dataType: "json",   
	        async: true, //默认为true 异步     
	        error: function(){     
	        	layer.alert("ERROR!", 8, !1);   
	        },     
	        success: function(data){
	        	if(data.status=="success"){
		        	layer.msg("删除成功",{icon: 1});  
			    }else{
			    	layer.alert(data.message);   
				}
	        	window.myTable.ajax.reload();    
	        }
	    });
	});
}

</script> 