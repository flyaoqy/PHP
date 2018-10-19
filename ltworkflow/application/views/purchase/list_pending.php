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
				</div>
			</div>				
		</form>
		<div class="search_list" style="margin-top: 20px">
		<table class="datalist4" datatable style="overflow: auto; box-sizing: border-box">
			<thead>
				<tr>
											<th nowrap>送审时间</th>
											<th nowrap>起草人工号</th>
											<th nowrap>起草人姓名</th>
											<th nowrap>合同名称</th>
											<th nowrap>合同类型</th>
											<th nowrap>物品类型</th>
											<th nowrap>合计金额</th>

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
		'sAjaxSource':'<?php echo site_url('purchase/list_pending/query') ?>',
		'order': [0,'desc'] ,
		'columns':[
			{sName:'cs_endtime',data:'cs_endtime',className:'dt_nowrap'},
			{sName:'userno',data:'userno',className:'dt_nowrap'},
			{sName:'username',data:'username',className:'dt_nowrap'},
			{sName:'contract_name',data:'contract_name',className:'dt_nowrap'},
			{sName:'contract_type',data:'contract_type',className:'dt_nowrap'},
			{sName:'goods_type',data:'goods_type',className:'dt_nowrap'},
			{sName:'contract_sum',data:'contract_sum',className:'dt_nowrap'},
			{sName:'id',data:'id',className:'dt_nowrap',
				render:function(data,type,row){
					var id =  data;
				 	var url = '<?php echo site_url("purchase/purchase/open?id=")?>'+id;
					var	operate = '<a href="'+url+'" >审批</a> ';
	                return operate;
        		}
        	}
		]
	});
	
	$('#search_btn').bind('click',function(){
		window.myTable.ajax.reload();
	});
});


</script> 