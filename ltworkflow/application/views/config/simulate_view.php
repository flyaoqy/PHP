
<div class="container-fluid" style="border: 1px solid #2979b4;padding:50px 20px 40px 20px;">
<div class="row" style="text-align:right">
    <div class="col-md-offset-7 col-sm-5 col-sm-offset-7 col-xs-6 col-xs-offset-6 ">
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="save();">执行
    	</button>
        <button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="window.history.go(-1);" >返回
        </button>
    </div>
</div>

<form class="form-horizontal" style="margin-bottom:3px;" role="form" id="theForm" method="post" action="<?= site_url('config/simulate/draw_flow') ?>">

	<div class="form-group form-group-sm" style="margin-top: 20px">
		<div class="row">
			<label class="col-sm-2 col-xs-2 control-label input-sm">etuid：</label>
			 <div class="col-sm-6 col-xs-6">
	        	<input type="text"  class="form-control input-sm" name="etuid" id="etuid" value=""/>
	    	 </div>
	    </div>
    </div>
</form>

</div>

<script type="text/javascript">

	$(function(){

	});

	function save(){
		//$('#theForm').submit();
		var url = "<?php echo site_url('config/simulate/draw_flow') ?>"+"?etuid="+$('#etuid').val();
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
		    content: url
		});
	}
</script>