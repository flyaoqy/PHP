<div class="container-fluid" style="border: 1px solid #2979b4;padding:50px 20px 40px 20px">
<div class="row" style="text-align:right">
    <div >
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="save();">保存</button>
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="exec();">执行</button>
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="excel();">当前结果Excel</button>
    	<!--  
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="compare_excel();">对比结果Excel</button>
       	-->
        <button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="window.history.go(-1);" >返回
        </button>
    </div>
</div>

<form class="form-horizontal" style="margin-bottom:3px;" role="form" id="theForm" method="post" action="<?php echo site_url('config/wftest/testutil/save') ?>">
	<input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
	<input type="hidden" name="execflag" id="execflag" value="0" />
	<div class="back_title">
    <h2>基本信息</h2>
	</div>
	<div class="form-group form-group-sm" style="margin-top: 20px">

		<div class="row">
		<label class="col-sm-2 col-xs-2 control-label input-sm">名称：</label>
		 <div class="col-sm-4 col-xs-4">
        	<input type="text"  class="form-control input-sm" name="tc_name" id="tc_name"
               value="<?php echo @$base['tc_name']; ?>"/>
    	 </div>
		</div>
		<div class="row">
    	 <label class="col-sm-2 col-xs-2 control-label input-sm">连接标记：</label>
		 <div class="col-sm-4 col-xs-4">
        	<input type="text"  class="form-control input-sm" name="tc_joinmark" id="tc_joinmark"
               value="<?php echo @$base['tc_joinmark']; ?>"/>
    	 </div>
		</div>
		<div class="row">
		<label class="col-sm-2 col-xs-2 control-label input-sm">描述：</label>
		 <div class="col-sm-10 col-xs-10">
    		<textarea class="form-control input-sm" maxLength='1000' style="width:762px;height:60px;" id="tc_discript" name="tc_discript"><?php echo @$base['tc_discript'];?></textarea>
    	 </div>
    	 </div>
    	 <div class="row">
		<label class="col-sm-2 col-xs-2 control-label input-sm">配置信息：</label>
		 <div class="col-sm-10 col-xs-10">
        	<textarea class="form-control input-sm" style="height:500px;width:100%;overflow-y:auto" id="tc_xmlcontent" name="tc_xmlcontent"><?php echo @$base['tc_xmlcontent'];?></textarea>
    	 </div>
    	 </div>
	</div>
</form>
</div>

<script type="text/javascript">
	var baseurl = "<?php echo base_url()?>";
	$(function(){
		//自适应高度
		$("#tc_xmlcontent").height($("#tc_xmlcontent")[0].scrollHeight);
	});
	//保存页面
	function save(){
		var loadform = layer.load(1);
		$('#theForm').ajaxSubmit({
			dataType:'json',
			error:function(request, status, error){
				layer.close(loadform);
			},
			success:function(res){
				layer.close(loadform);
				if(res.status == 'success'){
					layer.alert('保存成功');
					$("#execflag").val('0');
				}else{
					layer.alert(res.message);
				}
				
			}
		});
	}
	//执行测试
	function exec(){
		$("#execflag").val('1');
		save();
		$("#execflag").val('0');
	}
	//当前结果
	function excel(){
		window.open(baseurl+"index.php/config/wftest/testutil/excel?id="+$("#id").val());
	}
	//对比结果
	function compare_excel(){
		window.open(baseurl+"index.php/config/wftest/testutil/compare_excel?id="+$("#id").val());
	}



</script>

