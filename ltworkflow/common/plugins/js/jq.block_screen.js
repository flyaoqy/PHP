
/*
	封屏_zhouxiang_2014-7-23 11:19:48
*/

(function(){
	$.jq_block = function(msg){
		if(!msg){
			msg = '请稍候';
		}
		var dialog_element = $(''
			+'<div>'
			+'	<div class="jq_block_screen_msg" style="margin-top:15px;"><div class="jq_block_screen_image">'+msg+'</div></div>'
			+'</div>'
		);
		var dialog = dialog_element.dialog({
			modal:true,
			width:250,
			height:60,
			dialogClass:'jq_block_screen_dialog',
			resizable:false
		});
		return {
			close : function(){
				dialog.dialog('close');
			}
		};
	};
})();
