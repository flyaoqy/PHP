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
		<form class="form-inline" role="form" id="theForm" name="theForm" dt_form method="POST" action="" >
		<input type="hidden" name="excel_orderby" />

		<!--表单信息-->
		<div class="search-condition" dt_search_information>
			<div class="row">
			<!-- 查询条件 -->
								<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
					<input type="text" class="form-control" name="username" placeholder="起草人姓名">
					</div>
														<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
					<input type="text" class="form-control" name="contract_name" placeholder="合同名称">
					</div>
														<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
					<input type="text" class="form-control" name="contract_type" placeholder="合同类型">
					</div>
														<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
					<input type="text" class="form-control" name="goods_type" placeholder="物品类型">
					</div>
									
			</div>
			<div class="row" style="margin-top: 20px">
				<div class="col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6 col-md-6 col-md-offset-6">
				<button type="button" id="search_btn" class="btn btn-primary btn-sm">搜索</button>
				&nbsp;&nbsp;
				<button type="button"class="btn btn-primary btn-sm"  onclick="window.location.href='<?php echo site_url("purchase/purchase/open")?>'" >新建</button>
				</div>
			</div>				
		</form>
		<div class="search_list" style="margin-top: 20px">
		<table class="datalist4" datatable style="overflow: auto; box-sizing: border-box">
			<thead>
				<tr>
					<th nowrap>流程名称</th>
					<th nowrap>创建时间</th>
					<th nowrap>起草人工号</th>
					<th nowrap>起草人姓名</th>
					<th nowrap>合同名称</th>
					<th nowrap>合同类型</th>
					<th nowrap>物品类型</th>
					<th nowrap>合计金额</th>
					<th align="center" nowrap>流程状态</th>
					<th align="center" nowrap>操作</th>
				</tr>
			</thead>
			<tbody id="content">
			</tbody>
		</table>
		</div>
		<form id="formHidden" name='formHidden' action='' method='post'>
			<input type='hidden' name='et_uid' />
		</form>
</div>
<script language="javascript">

$(function() {
   	// 此方法调用在function.js，对原始方法进行了封装，可以重复定义已封装属性进行覆盖
	window.myTable = $('#datatable_container').initDataTable({
		'sAjaxSource':'<?php echo site_url('purchase/list_purchase/query') ?>',
		'order': [1,'desc'] ,
		'columns':[
			{sName:'wf_name',data:'wf_name',className:'dt_nowrap'},
			{sName:'createtime',data:'createtime',className:'dt_nowrap'},
			{sName:'userno',data:'userno',className:'dt_nowrap'},
			{sName:'username',data:'username',className:'dt_nowrap'},
			{sName:'contract_name',data:'contract_name',className:'dt_nowrap'},
			{sName:'contract_type',data:'contract_type',className:'dt_nowrap'},
			{sName:'goods_type',data:'goods_type',className:'dt_nowrap'},
			{sName:'contract_sum',data:'contract_sum',className:'dt_nowrap'},
			{sName:'id',data:'id',className:'dt_nowrap',
				render:function(data,type,row){
					var id =  data;
					var et_uid =  row['et_uid'];
					var et_state = row['et_state'];
				 	var url = '<?php echo site_url("purchase/purchase/open?id=")?>'+id;
				 	var operate = '';
					if(et_state == '5'){
						operate = '审批完成';
					}else{
						operate = "<a onclick=flowPreview('"+et_uid+"') >流程预览</a> | <a onmouseover=get_approver(this,'"+et_uid+"') >审批节点</a>";
					}	
				 	 return operate;
        		}
        	},
        	{sName:'id',data:'id',className:'dt_nowrap',
				render:function(data,type,row){
				var id =  data;
				var et_uid =  row['et_uid'];
				var et_state = row['et_state'];
			 	var url = '<?php echo site_url("purchase/purchase/open?id=")?>'+id;
			 	var	operate = '<a href="'+url+'" >查看</a> '
			 	if(et_state == '1'){
			 		operate += '| <a href="javascript:void(0)" onclick="del(\''+id+'\')">删除</a>';
		               
				}
			 	return operate;
    		}
    	}
		]
	});
	
	$('#search_btn').bind('click',function(){
		window.myTable.ajax.reload();
	});
});

//删除
function del(id){

	layer.confirm("确定要删除这条数据吗？",function(){
		var url = "<?php echo site_url('purchase/list_purchase/delete');?>";
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
/**
 * 流程预览
 */
function flowPreview(et_uid) {
	layer.open({
	    type: 2,
	    title: false,
	    area: ['800px', '515px'],
	    fix: false,
	    shadeClose: true,
	    closeBtn: [1, true],
	    offset: ['15px', ''], //上留15px
	    border: [0],
	    shade : [0.5, '#000'],
	    content: '<?php echo site_url('config/simulate/draw_flow?etuid=')?>' + et_uid
	});
}
/**
 * 当前审批人
 */
 function get_approver(obj,etuid){
	var url = "<?php echo site_url('tools/wf_interface/get_approver')?>";
	$.ajax({     
        url: url,    
        type: 'post',     
        data: 'etuid=' + etuid,
        dataType: "json",   
        async: true, //默认为true 异步     
        error: function(){     
        	layer.alert("ERROR!", 8, !1);   
        },     
        success: function(data){
        	var users = "";
        	var nodename = "";
        	$(data).each(function(){
        		nodename = this.cs_nodename;
        		users += this.name+",";
            });
        	if(users != ""){
        		var content = "";
            	if(nodename == ''){
            		content = users;
                }else{
                	content = nodename +"："+ users;
                }
        		layer.tips(content, obj,
	        			{tips: [2,'#8ECAF7'], time: 2000}
	        			);
        	}
        	
        }
    });
	
	
}
</script> 