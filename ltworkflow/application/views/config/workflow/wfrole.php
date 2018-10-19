<div class="back_title">
    <h2>审批人列表</h2>
</div>
<div class="row" style="text-align: left;    margin-left: 8px;"> 
    <button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="role_edit();">添加
    </button>
    
   <a role="button" class="btn btn-primary btn-link" target="_blank" href="index.php/config/workflow/list_variate/loadView?wf_uid=<?php echo $wfuid; ?>">变量列表</a>
</div>
<input type="hidden" name="wfuid" id="wfuid" value="<?php echo $wfuid; ?>"  />
<input type="hidden" name="stepid" id="stepid" value="<?php echo $stepid; ?>"  />

	<div class="table-responsive">
	  <table class="table">
	   <tr>
		   <th width="35%">审批人</th>
		   <th width="40%">判断条件</th>
		   <th width="5%">排序</th>
		   <th width="20%">操作</th>
	   </tr>
	   <tbody id="content">
	   </tbody>
	  </table>
	</div>

</div>
<div id="dialog_div" style="display:none">
	<input type="hidden" name="user_list" id="user_list" value=""  />
	<div>
	<span style="margin-left:10px">审批人：<button type="button" class="btn btn-xs btn-primary"  onclick="select_member()">选择</button></span>
	</div>
	<div>
	<textarea style="margin-left:10px;width:600px" rows="5" id="user_list_show" name="user_list_show"></textarea>
	</div>
	<div >
	<span style="margin-left:10px">判断条件：</span>
	</div>
	<div>
	<textarea style="margin-left:10px;width:600px" rows="5" id="expression" name="expression"></textarea>
	</div>
	<div >
	<span style="margin-left:10px">排序：<input type="text" id="sort" name="sort" value="0" /></span>
	</div>
</div>
<script src="common/plugins/js/jq_validate.js" type="text/javascript"></script>
<link href="common/plugins/person/person.css" rel="stylesheet"/>
<script src="common/plugins/person/jquery.personmultiple.js" type="text/javascript"></script>

<script type="text/javascript">

	$(function(){
		role_query();
	});
	
	//选择成员
	function select_member(){
		$.personSearchMultiple({
			single: true,
			spanid:"user_list",
			spanshow:"user_list_show",
			complate: function(data){
				$('#user_list').val(data.persons);
				$('#user_list_show').val(data.persons_show);			
			}
		}); 
	}
	//查询所有审批人列表
	function role_query(){
		$.ajax({
			type:"POST",
			dataType:"json",	
			async:true,
			url:"index.php/config/workflow/wfapprove/role_query",
			data: "wfuid=" + $("#wfuid").val()+
				"&stepid="+ $("#stepid").val(),
			success:function(data){
				$("#content").empty();
				var html = '';
				for(var i=0;i<data.length;i++){
					var row = data[i];
					html += '<tr>'
					html += '<td><textarea class="form-control input-sm" rows="3" readonly >'+row.showList+'</textarea></td>';
					html += '<td><textarea class="form-control input-sm" rows="3" readonly >'+row.expression+'</textarea></td>';
					html += '<td>'+row.sort+'</td>';
					html += '<td nowrap="nowrap"><button type="button" class="btn btn-primary btn-xs" onclick="role_open(\''+row.id+'\')" >编辑</button>';
					html += '<button type="button" class="btn btn-danger btn-xs" onclick="role_del(\''+row.id+'\')" >删除</button></td>';
					html += '</tr>'
				}
				$("#content").append(html);
			}
		});


	}
	//打开编辑窗口
	function role_open(id){
		$.ajax({
			type:"POST",
			dataType:"json",	
			url:"index.php/config/workflow/wfapprove/role_info",
			data: "id=" + id,
			success:function(data){
				role_edit(data);
			}
		});
	}
	//删除
	function role_del(id){
		layer.confirm("确定要删除这条数据吗？",function(){
			$.ajax({
				type:"POST",
				dataType:"json",	
				url:"index.php/config/workflow/wfapprove/role_del",
				data: "id=" + id,
				success:function(data){
					if(data.status=="success"){
						layer.alert('删除成功',{icon: 1});
						role_query();
					}else{
						layer.alert(data.msg);
					}
				}
			});
		});
	}
	//编辑窗口
	function role_edit(data){
		var id = "";
		var persons = "";
		var expression = "";
		var group_uid = "";
		var sort = "0"
		if(data != undefined ){
			persons = data.showList;
			expression = data.expression;
			id = data.id;
			group_uid = data.group_uid;
			sort = data.sort;
			$("#user_list").val(data.snList);
		}else{	
			$("#user_list").val("");
		}
		$("#user_list_show").val(persons);
		$("#expression").val(expression);
		$("#sort").val(sort);

		$("#dialog_div").dialog({
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
						url:"index.php/config/workflow/wfapprove/role_save",
						data: "id=" + id+
							"&group_uid="+group_uid+
							"&wfuid="+$("#wfuid").val()+
							"&stepid="+$("#stepid").val()+
							"&sort="+$("#sort").val()+
							"&user_list="+$("#user_list").val()+
							"&user_list_show="+$("#user_list_show").html()+
							"&expression="+escape($("#expression").val()),
						success:function(data){
							if(data.status=="success"){
								layer.alert('保存成功',{icon: 1});
								$("#dialog_div").dialog('close');
								role_query();
							}else{
								layer.alert(data.msg);
							}
						}
					});
				}
			}
		});

	}
</script>