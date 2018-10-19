
/* 
 * ----------------- 2013-9-24_zhouxiang -----------------
 * ERP下拉列表变换
 * 将下拉列表变换为可进行搜索的下拉选择框
 * ----------------- 2014-5-21_zhouxiang -----------------
 * 适应SIAS系统作出调整
 */
(function($){
	
	// 修改值
	$.fn.select_pro_val = function(value){
		var ipt = $(this);
		var div = ipt.parents('.select_pro_wrapper').find('.select_pro_show');
		ipt.val(value);
		div.html(value);
	},
	
	$.fn.select_pro = function(attr){

		if(attr){
			var change_func = attr.on_change;
		}
		
		// 如果是disabled则不处理
		if($(this).attr('disabled')){
			return;
		}
		
		// 获取option列表
		var optionList = new Array();
		
		$(this).find('option').each(function(){
			var element = {text:$(this).text(),value:$(this).attr('value')};
			optionList.push(element);
		});
		
		var divElement = $('<div class="select_pro_wrapper"></div>');			// 整体
		var spanElement = $('<div class="select_pro_show"></div>');				// 显示文字
		var buttonElement = $('<div class="select_pro_button"></div>');			// 向下箭头按钮
		var endLine = $('<div class="select_pro_endLine"></div>');				// 清除浮动
		var inputElement = $('<input type="hidden" />');						// 隐藏输入框（表单提交）
		
		// 将原select的name、id、value属性赋给新的input元素
		inputElement.attr('name',$(this).attr('name'));
		inputElement.attr('id',$(this).attr('id'));
		inputElement.val($(this).val());
		
		// 将原select的选中文本赋值给显示文字
		spanElement.text($(this).find('option:selected').text());
		divElement.attr('select_pro_length',$(this).attr('select_pro_length'));
		
		// 边距处理
		divElement.css('margin-left',$(this).css('margin-left'));
		divElement.css('margin-top',$(this).css('margin-top'));
		divElement.css('margin-right',$(this).css('margin-right'));
		divElement.css('margin-bottom',$(this).css('margin-bottom'));
		
		// 继承原select的宽度，计算显示文字的宽度，非固定宽度使用auto特殊处理
		divElement.css('width',$(this).css('width'));
		var spanWidth = parseInt($(this).css('width'))-30;
		if(!spanWidth){
			spanWidth = 'auto';
		}else{
			spanWidth = spanWidth + 'px';
		}
		spanElement.css('width',spanWidth);
		
		// 拼装
		spanElement.appendTo(divElement);
		buttonElement.appendTo(divElement);
		inputElement.appendTo(divElement);
		endLine.appendTo(divElement);
		
		// 搜索列表部分
		var searchDiv = $('<div class="select_pro_searchDiv"></div>'); 		// 外框
		var searchInput = $('<input class="select_pro_searchInput" />');	// 输入框
		var hintDiv = $('<div class="select_pro_inputHint">搜索：</div>'); 	// 提示信息
		searchDiv.css('z-index','100');
		
		var selectDiv = $('<div class="select_pro_selectDiv" ></div>');		// 列表框
		hintDiv.appendTo(searchDiv);
		searchInput.appendTo(searchDiv);
		selectDiv.appendTo(searchDiv);
		
		
		searchDiv.appendTo(divElement);
		searchDiv.hide();	// 隐藏列表框

		// 将所有的option添加到列表框内
		for(var optionIndex = 0 ; optionIndex < optionList.length ; optionIndex++){
			var option = optionList[optionIndex];
			var optionDiv = $('<div class="select_pro_option" opt_value="'+option.value+'">'+option.text+'</div>');
			optionDiv.appendTo(selectDiv);
			optionDiv.bind('click',function(e){
				var text = $(this).text();
				var value = $(this).attr('opt_value');
				spanElement.text(text);
				inputElement.val(value);
				searchDiv.hide();
				if(change_func){
					change_func(inputElement);
				}
				return false;
			});
		}
		
		var noResultHint = $('<div class="select_pro_noresult"></div>');
		noResultHint.appendTo(selectDiv);
		
		noResultHint.hide();
		
		//确认最大宽度
//		var maxWidth = 0;
//		var hideElement = $('<span style="display:none;"></span>');
//		$('body').append(hideElement);
//		selectDiv.find('.select_pro_option').each(function(){
//			hideElement.text($(this).text());
//			var width = hideElement.width();
//			if(width > maxWidth){
//				maxWidth = width;
//			}
//		});
//		hideElement.detach();
//		
//		var hideElement = $('<span style="display:none;"></span>');
//		$('body').append(hideElement);
//		for(var index in optionList){
//			hideElement.text(optionList[index].text);
//			var width = hideElement.width();
//			if(width > maxWidth){
//				maxWidth = width;
//			}
//		};
//		hideElement.detach();
		
		// 搜索输入框发生改变时关联option显示
		function onInputChange(){
			var value = $(this).val();
			var allHide = true;
			searchDiv.find('div.select_pro_option').each(function(){
				if($(this).text().toLowerCase().indexOf(value.toLowerCase()) != -1){
					$(this).show();
					allHide = false;
				}else{
					$(this).hide();
				}
				if(allHide){
					noResultHint.text('没有与"'+value+'"匹配的选项...');
					noResultHint.show();
				}else{
					noResultHint.hide();
				}
			});
		}
		
		searchInput.bind('propertychange',onInputChange); // IE
		searchInput.bind('input',onInputChange); // 其他
		
		searchInput.bind('keydown',function(e){
			if(e.keyCode == '13'){
				var firstElement = $(searchDiv.find('.select_pro_option:visible').first());
				if(firstElement.text() != ''){
					firstElement.click();
				}
			}
		});
		
		// 点击时显示/隐藏搜索列表
		divElement.bind('click',function(){
			if(searchDiv.css('display')=='none'){
				$('.select_pro_searchDiv').hide();
				adjustPosition();
				searchDiv.show();
				searchInput.focus();
				adjustWidth();
			}else{
				searchDiv.hide();
			}
			return false;
		});
		
		// 阻止事件冒泡
		searchDiv.bind('click',function(){
			return false;
		});
		
		// 放上新的，删掉那个不要的
		divElement.insertAfter($(this));
		$(this).detach();
		
		
		// 给body注册事件，搜索列表，仿select效果
		$('body').bind('click',function(){
			searchDiv.hide();
		});
		
		// 调整位置（前处理）
		function adjustPosition(){
			var posX = divElement.offset().left;
			var posY = divElement.offset().top + divElement.height()+2 ;
			searchDiv.css('left',posX+'px');
			searchDiv.css('top',posY+'px');
//			searchDiv.css('width',divElement.width());
//			var width = parseInt(divElement.width());
//			if(maxWidth > width){
//				searchDiv.css('width',(maxWidth+20)+'px');
//			}else{
//				searchDiv.css('width',(width+((maxWidth+20)<width?0:20))+'px');
//			}
		}
		
		// 调整宽度（后处理）
		function adjustWidth(){
			var elementWidth = parseInt(divElement.width());
			var selectWidth = parseInt(selectDiv.width());
			var width = elementWidth>selectWidth?elementWidth:selectWidth;
			searchDiv.css('width',width + 'px');
			searchInput.width(width - 40);
		}
		
		return {
			getText : function(){
				return spanElement.text();
			}
		};
		
	};
	
})(jQuery);

//变换所有下拉列表
function change_select_pro(){
	// 将所有标记select_pro的元素变为带有搜索的组件
	$('select[select_pro]').each(function(){
		var opt = $(this).select_pro();
	});
}




