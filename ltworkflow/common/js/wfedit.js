/**
 * 编辑节点
 * @param wfuid
 * @param stepid
 * @param name
 */
function node_edit(wfuid,stepid,name) {
	var mySavedModel = $("#mySavedModel").val();
	var obj = jQuery.parseJSON(mySavedModel);
	var linkDataArray = obj.linkDataArray;
	var configRole = false;
	var role_value = "";
	for(var i=0;i<linkDataArray.length;i++){
		var linkdata = linkDataArray[i];
		if(linkdata.to == stepid){
			role_value = linkdata.role_value;
			if(linkdata.role_type == "2"){
				configRole = true;	
			}
		}		
	}
	if(configRole){//配置界面
		var html = "<iframe frameBorder='0' width='100%' height='100%' src='index.php/config/workflow/wfapprove/role_list?wfuid="+wfuid+"&stepid="+stepid+"'></iframe>";
		$("#dialog_node").html(html).dialog({
			width: 950,height: 600,
			title: name,
			resizable:false,
			modal: true,
			buttons:{}
		});
	}else{
		htmlstr = '';
		htmlstr += '<div >';
		htmlstr += '<span style="margin-left:10px">环节：</span>';
		htmlstr += name;
		htmlstr += '</div>';
		htmlstr += '<div >';
		htmlstr += '<span style="margin-left:10px">判断条件：</span>';
		htmlstr += role_value;
		htmlstr += '</div>';
		
		$("#dialog_node").html(htmlstr).dialog({
			title:"审批条件",
			resizable:false,
			width:650,Height:550,
			modal:true,
			buttons: {}
		});
		
	}
	

}
/**
 * 编辑审批条件
 * @param wfuid
 * @param from
 * @param to
 */
function link_edit(wfuid,from,to,condition_type,condition_value){
	$.ajax({
		type:"POST",
		dataType:"json",	
		async:true,
		url:"index.php/config/workflow/wfapprove/condition_get",
		data: "wfuid=" + wfuid+
			"&from="+from+
			"&to="+to,
		success:function(data){
			$("#expression").text(data.expression);
			$("#from_to").text("("+data.from_name+")至("+data.to_name+")");
		}
	});
	
	if(condition_type == "config"){//如果是配置项，打开配置页面
		htmlstr = '';
		htmlstr += '<div >';
		htmlstr += '<span style="margin-left:10px">环节：</span>';
		htmlstr += '<span id="from_to"></span>';
		htmlstr += '</div>';
		htmlstr += '<div >';
		htmlstr += '<span style="margin-left:10px">变量：</span>';
		htmlstr += '<a target="_blank" href="index.php/config/workflow/list_variate/loadView?wf_uid='+wfuid+'">变量列表</a>';
		htmlstr += '</div>';
		htmlstr += '<div >';
		htmlstr += '<span style="margin-left:10px">判断条件：</span>';
		htmlstr += '</div>';
		htmlstr += '<div>';
		htmlstr += '<textarea style="margin-left:10px;width:600px" rows="5" id="expression"></textarea>';
		htmlstr += '</div>';
		
		$("#dialog_div").html(htmlstr).dialog({
			title:"审批条件",
			resizable:false,
			width:650,Height:550,
			modal:true,
			buttons: {
				'取消': function() {
					$(this).dialog('close');	
				},
				'确定': function() {				
					$.ajax({
						type:"POST",
						dataType:"json",	
						async:true,
						url:"index.php/config/workflow/wfapprove/condition_save",
						data: "wfuid=" + wfuid+
							"&from="+from+
							"&to="+to+
							"&expression="+escape($("#expression").val()),
						success:function(data){
							if(data.status=="success"){
								layer.alert('保存成功',{icon: 1});
								$("#dialog_div").dialog('close');
							}else{
								layer.alert(data.msg);
							}
						}
					});
				}
			}
		});
		
	}else{//只读页面
		htmlstr = '';
		htmlstr += '<div >';
		htmlstr += '<span style="margin-left:10px">环节：</span>';
		htmlstr += '<span id="from_to"></span>';
		htmlstr += '</div>';
		htmlstr += '<div >';
		htmlstr += '<span style="margin-left:10px">判断条件：</span>';
		htmlstr += condition_value;
		htmlstr += '</div>';
		
		$("#dialog_div").html(htmlstr).dialog({
			title:"审批条件",
			resizable:false,
			width:650,Height:550,
			modal:true,
			buttons: {}
		});
		
	}
	
	
	
}
