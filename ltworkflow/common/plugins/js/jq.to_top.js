
/*
	返回顶部_zhouxiang_2014-7-29 18:32:30
	注：样式 btn btn-warning 为 bootstrap 样式
	-------------------------------------------
	2014-7-30 16:08:24_zhouxiang_修改
	增加位置选择、偏移量、透明度、动画效果开关
	取消了 bootstrap 样式要求，独立为 css 样式
*/

(function(){
	
	$.jq_to_top_clear = function(){
		$('.jq_to_top_element').detach();
	};
	
	// 初始化
	$.jq_to_top = function(options){
	
		// 如果之前初始化过，删除之前的
		$.jq_to_top_clear();
	
		if(options === undefined){
			options = {};
		}

		var default_values = {
			position : 'right_bottom',		// 位置[left_top|left_bottom|right_top|right_bottom]
			padding_x : 50,					// 横向偏移量
			padding_y : 100,				// 纵向偏移量
			alpha : 0.8,					// 透明度
			animation:true					// 滚动效果
		};
		
		for(var index in default_values){
			if(options[index] === undefined){
				options[index] = default_values[index];
			}
		}
		
		var pos_arr = options.position.split('_') ;
		var var_x = pos_arr[0] ;
		var var_y = pos_arr[1] ;
	
		var element = $('<button class="jq_to_top_element">▲</button>');
		
		element.css(var_x,options.padding_x);
		element.css(var_y,options.padding_y);
		element.css('opacity',options.alpha);
		
		// 鼠标上移透明度改变
		element.hover(function(){
			element.css('opacity',1);
		},function(){
			element.css('opacity',options.alpha);
		});
		
		$('body').append(element);

		// 步数
		var steps = 20;
		element.bind('click',function(){
			if(options.animation){
				// 总滚动高度
				var total_height = $(window).scrollTop();
				// 滚动步幅
				var each_step = total_height/steps;
				var interval = setInterval(function(){
					window.scrollBy(0,-each_step);
					if($(window).scrollTop() == 0){
						clearInterval(interval);
					}
				},1000/60);
			}else{
				window.scroll(0,0);
			}
		});
		
		// 显隐
		$(window).bind('scroll',function(){
			if($(window).scrollTop() > 200 ){
				element.show();
			}else{
				element.hide();
			}
		});
		
	};
})();