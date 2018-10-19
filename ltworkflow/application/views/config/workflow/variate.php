
<div class="container-fluid" style="border: 1px solid #2979b4;padding:50px 20px 40px 20px">
<div class="row" style="text-align:right">
    <div class="col-md-offset-7 col-sm-5 col-sm-offset-7 col-xs-6 col-xs-offset-6 ">
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="save();">保存
    	</button>
        <button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="window.history.go(-1);" >返回
        </button>
    </div>
</div>

<form class="form-horizontal" style="margin-bottom:3px;" role="form" id="theForm" method="post" action="<?= site_url('config/workflow/variate/save') ?>">
	<input type="hidden" name="id" id="id" value="<?php echo $base['id']; ?>"  />
	<input type="hidden" name="wf_uid" id="wf_uid" value="<?php echo $base['wf_uid']; ?>"  />
	<div class="back_title">
    <h2>基本信息</h2>
	</div>
	<div class="form-group form-group-sm" style="margin-top: 20px">

				 <label class="col-sm-2 col-xs-2 control-label input-sm">变量：</label>
		 <div class="col-sm-2 col-xs-2">
        	<input type="text"  class="form-control input-sm" name="expression_key" id="expression_key"
               value="<?php echo @$base['expression_key']; ?>"/>
    	 </div>

				 <label class="col-sm-2 col-xs-2 control-label input-sm">值：</label>
		 <div class="col-sm-6 col-xs-6">
        	<input type="text"  class="form-control input-sm" name="expression_value" id="expression_value"
               value="<?php echo @$base['expression_value']; ?>"/>
    	 </div>

		
	</div>
</form>

</div>
<script src="common/plugins/js/jq_validate.js" type="text/javascript"></script>
<script type="text/javascript">

	$(function(){
		// 验证算法
		jq_validate.set_validate({
			url:'<?php echo site_url('config/workflow/variate/validate'); ?>'
		});
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
						window.location.href = '<?php echo site_url('config/workflow/list_variate/loadView?wf_uid='.$base['wf_uid']); ?>';
					});
				}else{
					show_message(json.msg);
				}
			}
		});
	}
</script>