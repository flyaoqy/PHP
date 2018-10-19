
<div class="container-fluid" style="border: 1px solid #2979b4;padding:50px 20px 40px 20px">
<div class="row" style="text-align:right">
    <div class="col-md-offset-7 col-sm-5 col-sm-offset-7 col-xs-6 col-xs-offset-6 ">
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="save();">保存
    	</button>
        <button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="window.history.go(-1);" >返回
        </button>
    </div>
</div>

<form class="form-horizontal" style="margin-bottom:3px;" role="form" id="theForm" method="post" action="<?= site_url('config/workflow/role/save') ?>">
	<input type="hidden" name="id" id="id" value="<?php echo $base['id']; ?>"  />
	<input type="hidden" name="wf_uid" id="wf_uid" value="<?php echo $base['wf_uid']; ?>"  />
	<input type="hidden" name="group_uid" id="group_uid" value="<?php echo $base['group_uid']; ?>"  />
	<input type="hidden" name="type" id="type" value="custom_role"  />
	<input type="hidden" name="user_list" id="user_list" value=""  />
	<div class="back_title">
    <h2>基本信息</h2>
	</div>
	<div class="form-group form-group-sm" style="margin-top: 20px">

		<div class="row">
		<label class="col-sm-2 col-xs-2 control-label input-sm">角色名称：</label>
		 <div class="col-sm-4 col-xs-4">
        	<input type="text"  class="form-control input-sm" name="expression" id="expression"
               value="<?php echo @$base['expression']; ?>"/>
    	 </div>
		</div>
		<div class="row">
		<label class="col-sm-2 col-xs-2 control-label input-sm">是否全局：</label>
		 <div class="col-sm-4 col-xs-4">
        	<input type="radio"  name="isGlobal" id="isGlobal" 
               value="1" <?php echo $base['wf_uid']==""?"checked":""; ?>/>是
            <?php if($base['wf_uid']!=""){?>   
            <input type="radio"  name="isGlobal" id="isGlobal"
               value="0" <?php echo $base['wf_uid']!=""?"checked":""; ?>/>否
              <?php }?>
    	 </div>
		</div>
		<div class="row">
		<label class="col-sm-2 col-xs-2 control-label input-sm">成员列表：</label>
		 <div class="col-sm-10 col-xs-10">
        	<textarea class="form-control input-sm" rows="5" id="user_list_show" name="user_list_show"></textarea>
    		<button type="button" class="btn btn-xs btn-primary"  onclick="select_member()">选择</button>
    	 </div>
    	 </div>


		
	</div>
</form>

</div>
<script src="common/plugins/js/jq_validate.js" type="text/javascript"></script>
<link href="common/plugins/person/person.css" rel="stylesheet"/>
<script src="common/plugins/person/jquery.personmultiple.js" type="text/javascript"></script>
<script type="text/javascript">

	$(function(){
		// 验证算法
		jq_validate.set_validate({
			url:'<?php echo site_url('config/workflow/role/validate'); ?>'
		});
		role_query();
	});

	function save(){
		if(jq_validate.check_invalid()){
			return;
		}
		$('#theForm').ajaxSubmit({
			dataType:'json',
			success : function(json){
				if(json.status == 'success'){
					layer.alert('保存成功',{icon: 1},function(){
						window.location.href = '<?php echo site_url('config/workflow/list_role/loadView?wf_uid='.$base['wf_uid']); ?>';
					});
				}else{
					show_message(json.msg);
				}
			}
		});
	}
	//查询所有审批人列表
	function role_query(){
		$.ajax({
			type:"POST",
			dataType:"json",	
			async:true,
			url:"index.php/config/workflow/role/role_query",
			data: "group_uid=" + $("#group_uid").val(),
			success:function(data){
				$("#user_list_show").val(data.showList);
				$("#user_list").val(data.snList);
			}
		});
	}
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
</script>