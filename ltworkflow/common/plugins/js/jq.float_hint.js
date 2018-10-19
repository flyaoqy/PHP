
/*
	浮层提示_zhouxiang_2014-7-21 18:46:46
*/

(function(){
	$.fn.float_hint = function(){
		$(this).each(function(){
			var element = $(this);
			if(element.attr('has_hint') == '1'){
				return;
			}else{
				element.attr('has_hint','1');
			}
			var hint_value = element.attr('hint');
			hint_value = hint_value.replace(/\\n/g,'<br/>');
			var hint_div = $('<div class="hint_element">'+hint_value+'</div>');
			element.bind('mouseover',function(event){
				$('body').append(hint_div);
			});
			element.bind('mouseout',function(){
				hint_div.detach();
			});
			element.bind('mousemove',function(event){
				var x = event.pageX;
				var y = event.pageY;
				hint_div.css('left',x+20);
				hint_div.css('top',y-10);
			});
		});
	};
})();