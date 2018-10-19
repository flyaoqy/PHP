
<div class="container-fluid" style="border: 1px solid #2979b4;padding:50px 20px 40px 20px">
<div class="row" style="text-align:right">
    <div>
    	<?php 
    	if($base['id'] == ""){?>
    		<button type="button" class="btn btn-primary btn-sm" onclick="demo.doAction(-11)" >保存
        	</button>
    	<?php }?>
    	
    	<?php 
    	if(!empty($workflow)){
    		foreach ($workflow['actions'] as $action) { ?>
            <button type="button" id="submitbutton" class="btn btn-primary btn-sm"
                    onclick="demo.doAction('<?php echo $action['id'] ?>');"><?php echo $action['name'] ?></button>
        <?php }
    	} ?>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.history.go(-1);" >返回
        </button>
    </div>
</div>

<form class="form-horizontal" style="margin-bottom:3px;" role="form" id="theForm" method="post" action="<?php echo site_url('purchase/purchase/save') ?>">
	<input type="hidden" name="id" id="id" value="<?php echo $base['id']; ?>"  />
	<input type="hidden" name="et_uid" id="et_uid" value="<?php echo $base['et_uid']; ?>"  />
	<input type="hidden" id="__actionid" name="__actionid" />
	<input type="hidden" id="__flowcomm" name="__flowcomm" />
	<input type="hidden" id="__router" name="__router" />
	<input type="hidden" id="former_url" value="<?php echo site_url("purchase/list_purchase/loadview")?>"/>
	
	<div class="back_title">
    <h2>基本信息</h2>
	</div>
	<div class="form-group form-group-sm" style="margin-top: 20px">
		<div class="row">
			<label class="col-sm-3 col-xs-3 control-label input-sm">审批流程：</label>
			 <div class="col-sm-3 col-xs-3 input-sm">
	        	<?php 
	        	if($base['id'] == ""){
	        		echo '<select name="wf_uid" class="form-control selectpicker show-tick show-menu-arrow" data-live-search="true">';
	        		foreach ($wflist as $row){
	        			echo '<option value="'.$row['id'].'">'.$row['value'].'</option>';
	        			
	        		}
	        		echo '</select>';
	        	}else{
	        		echo @$base['wf_name'];
	        	}
	        	?>
	    	 </div>
			<label class="col-sm-3 col-xs-3 control-label input-sm">创建时间：</label>
			 <div class="col-sm-3 col-xs-3 input-sm">
	        	<?php echo @$base['createtime']; ?>
	    	 </div>
    	 </div>
		<label class="col-sm-3 col-xs-3 control-label input-sm">起草人工号：</label>
		 <div class="col-sm-3 col-xs-3 input-sm">
        	<?php echo @$base['userno']; ?>
    	 </div>

		<label class="col-sm-3 col-xs-3 control-label input-sm">起草人姓名：</label>
		 <div class="col-sm-3 col-xs-3 input-sm">
        	<?php echo @$base['username']; ?>
    	 </div>

		<label class="col-sm-3 col-xs-3 control-label input-sm">合同名称：</label>
		 <div class="col-sm-3 col-xs-3">
        	<input type="text"  class="form-control input-sm" name="contract_name" id="contract_name"
               value="<?php echo @$base['contract_name']; ?>"/>
    	 </div>

		<label class="col-sm-3 col-xs-3 control-label input-sm">合计金额：</label>
		 <div class="col-sm-3 col-xs-3">
        	<input type="text"  class="form-control input-sm" name="contract_sum" id="contract_sum"
               value="<?php echo @$base['contract_sum']; ?>"/>
    	 </div>
	
		<label class="col-sm-3 col-xs-3 control-label input-sm">合同类型：</label>
		 <div class="col-sm-3 col-xs-3">
            <select class="form-control input-sm" name="contract_type" id="contract_type">
            	<option value="实物" <?php echo @$base['contract_type']=="实物"?"selected":"";?>>实物</option>
            	<option value="服务" <?php echo @$base['contract_type']=="服务"?"selected":"";?>>服务</option>
            	<option value="市场" <?php echo @$base['contract_type']=="市场"?"selected":"";?>>市场</option>
            </select>
    	 </div>

		<label class="col-sm-3 col-xs-3 control-label input-sm">物品类型：</label>
		 <div class="col-sm-3 col-xs-3">
            <select class="form-control input-sm" name="goods_type" id="goods_type">
            	<option value="办公用品" <?php echo @$base['goods_type']=="办公用品"?"selected":"";?>>办公用品</option>
            	<option value="办公电脑" <?php echo @$base['goods_type']=="办公电脑"?"selected":"";?>>办公电脑</option>
            	<option value="服务器" <?php echo @$base['goods_type']=="服务器"?"selected":"";?>>服务器</option>
            </select>
    	 </div>
    	 <div class="row" > 
	 		<label class="col-sm-3 col-xs-3 control-label input-sm">审批意见：</label>
		 	<div class="col-sm-9 col-xs-9">
			 <textarea class="form-control input-sm" name="__comm" id="__comm" 
			 rows="" cols=""></textarea>
			 
    	 </div>
	</div>

		
		
	</div>
	<?php if (!empty($workflow)) { ?>
	<div class="back_title">
    <h2>审批日志</h2>
	</div>
	<table class="table table-striped table-condensed">
	  <thead>
	  <tr>
			<th>审批意见</th>
            <th>审批人</th>
            <th>审批时间</th>
            <th>审批结果</th>
	  </tr>
	  </thead>
	  <tbody >
            <?php foreach ($workflow['logs'] as $row){
            $comment = $row['comment'];
            $comment = str_replace("\n", "<br>", $comment);
            ?>
            <tr>
                <td ><?php echo  $comment ?></td>
                <td ><?php echo $row['u_name'] ?></td>
                <td ><?php echo $row['finishdate'] ?></td>
                <td ><?php echo $row['wf_status'] ?></td>
            </tr>
            <?php } ?>
	  </tbody>
	</table>
	<?php } ?>
</form>

</div>
<div id="div_dialog"></div>
<script type="text/javascript" src="common/plugins/person/jquery.bootstrap.person.js"></script>
<script type="text/javascript" src="common/js/demo/demo.js"></script>
<script type="text/javascript">
var formGlobal = Object();
formGlobal.baseurl = "<?php echo base_url()?>";

$(function(){
	
});
</script>