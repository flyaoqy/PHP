<!doctype html>
<html lang="en">
<head>
<title>Draw flow</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo base_url()?>"/>

<link rel="stylesheet" type="text/css" href="common/css/jquery-ui-1.9.0.custom.css"/>

<script type="text/javascript" src="common/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="common/js/jquery-ui-1.9.0.custom.js"></script> 
<style>
.node_start{
	background:url("common/images/flow/start.gif") no-repeat;
	height:58px;
	width:143px;
	}
.node_end{
	background:url("common/images/flow/end.gif") no-repeat;
	height:58px;
	width:143px;
	}
.node_hsitory{
	background:url("common/images/flow/node_history.gif") no-repeat;
	height:60px;
	width:143px;
	}
.node_flow{
	background:url("common/images/flow/node_forward.gif") no-repeat;
	height:59px;
	width:143px;
	}

.node_name{
	position:absolute;
	padding-top:20px;
	font-size:17px;
	font-weight:bold;
	text-align:center;
	width:143px;
	}
.node_approve_person{
	position:absolute;
	margin:20px 150px;
	font-size:14px;
	text-align:left;
	}
.node_approve_status{
	position:absolute;
	margin:75px 90px;
	font-size:14px;
	text-align:left;
	}
.node_arrow_forward{
	background:url("common/images/flow/arrow_forward.png") no-repeat;
	height:52px;
	width:32px;
	margin-left:50px;
	}
.node_arrow_hsitory{
	background:url("common/images/flow/arrow_hsitory.png") no-repeat;
	height:52px;
	width:32px;
	margin-left:50px;
	}

</style>

</head>
<body>
<div style="background: #F7F7F7;">
	<input type='hidden' id='etuid' value='<?php echo $etuid?>' />
	<div id="folw_contents" style="margin-left:200px;">
		
	</div>
</div>
</body>
</html>

<script type='text/JavaScript'>
var baseurl = "<?php echo base_url()?>";
$(function(){
	load_data();
});


/**
 * 画流程
 */
function draw(data){
	var html = "";
	$("#folw_contents").empty();
	if(data.historyList.length == 0 && data.forwardList.length == 0){
		html = "没有流程图！";
		$("#folw_contents").append(html);
		return;
	}
	//开始
	html = "";
	html += "<div class='node_start'></div>";
	html += "<div class='node_arrow_hsitory'></div>";
	$("#folw_contents").append(html);
	
	//审批记录
	html = "";
	for(var i=0;i<data.historyList.length;i++){
		var obj = data.historyList[i];
		html += "<div class='node_hsitory'>";
		html += "<div class='node_name'>"+obj['node_name']+"</div>";
		html += "<div class='node_approve_person' ><span style='color:#0066ff;cursor:hand' erp_card_empid='"+obj['ssn']+"'>"+obj['name']+"</span></div>";
		html += "<div class='node_approve_status'>"+obj['status']+"("+obj['finishdate']+")</div>";
		html += "</div>";
		html += "<div class='node_arrow_hsitory'></div>";
	}
	$("#folw_contents").append(html);
	//审批预测
	html = "";
	for(var i=0;i<data.forwardList.length;i++){
		var obj = data.forwardList[i];
		var curUserListStr =  "";
		for(j=0;j<obj['curUserList'].length;j++){
			var user = obj['curUserList'][j];
			curUserListStr += "<span style='color:#0066ff;cursor:hand' erp_card_empid='"+user['ssn']+"'>"+user['name']+",</span>"
		}
		html += "<div class='node_flow'>";
		html += "<div class='node_name'>"+obj['node_name']+"</div>";
		html += "<div class='node_approve_person'>"+curUserListStr+"</div>";
		html += "</div>";
		html += "<div class='node_arrow_forward'></div>";
	}
	$("#folw_contents").append(html);
	//结束
	html = "";
	html += "<div class='node_end'></div>";
	$("#folw_contents").append(html);
}
/**
 * 加载流程数据
 */
function load_data(){
	$.ajax({
		type:"POST",
		dataType:"json",	
		async:true,
		url:baseurl+"index.php/config/simulate/draw_flow_data",
		data:"etuid="+$("#etuid").val(),
		success:function(data){
			draw(data);
		}
	});
}
</script>

