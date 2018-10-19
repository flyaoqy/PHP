


(function(){

	window.jq_validate = {};
	
	/*
	 * 通用表单验证前端JS_zhouxiang_2014-6-18
	 * 配合PHP helper/validate_helper.php 
	 * 详见PHP文件内说明
	 * --------------------------------------
	 * 2014-7-30 14:59:15_zhouxiang 修改
	 * 分离验证不通过结果集对象res_obj，验证完毕后统一加工，便于修改
	 */
	 
	 var jq_validate_valid_types = {
		'integer' : {
			'valid' : /^[0-9]+$/ , 
			'name' : '正整数' 
		},
		'float' : {
			'valid' : /^[0-9]+(.[0-9]+)?$/ , 
			'name' : '数字' 
		},
		'date' : {
			'valid' : /^[0-9]{4}-(\d{1,2})-(\d{1,2})$/ , 
			'name' : '日期' 
		},
		'date_month' : {
			'valid' : /^[0-9]{4}-(\d{1,2})$/ , 
			'name' : '日期年月' 
		},
		'email' : {
			'valid' : /^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/ ,
			'name' : '邮箱地址'
		}
	}
	 
	 
	// 标记验证
	jq_validate.set_validate = function (options){

		// 配置对象
		var data = options.rules;
		var params = options.data;
		
		// 如果没有，从服务器获取
		if(!data){
			var url = options.url;
			var send_params = {};
			if(params){
				for(var name in params){
					send_params[name] = params[name];
				}
			}
			send_params['from'] = 'js';
			$.ajax({
				url:url,
				type:'POST',
				data:send_params,
				dataType:'json',
				async:false,
				success:function(response){
					data = response;
				}
			});
		}
		
		var prefix = 'vali_';
		
		// 根元素
		var root = options.root;
		if(!root){
			root = $('body');
		}
		
		// 添加元素属性
		for(var name in data ){
			var element = root.find('[name="'+name+'"]');
			if(data[name]['is_array'] === true){
				element = root.find('[name="'+name+'[]"]');
			}
			if(data[name]['no_js'] === true){
				continue;
			}
			// 添加 type placeholder 提示
			var placeholder_msg = null;
			var type = data[name]['type'];
			if(type){
				placeholder_msg = '请填写'+jq_validate_valid_types[type]['name'];
			}
			var minlength = data[name]['minlength'];
			var maxlength = data[name]['maxlength'];
			if(placeholder_msg == null){
				if(maxlength){
					placeholder_msg = '请输入'+(minlength?(minlength+'至'):'')+maxlength+(type=='integer'?'位':'个字')+'以内';
				}else if(minlength){
					placeholder_msg = '请至少输入'+minlength+(type=='integer'?'位':'个字');
				}
			}
			var empty = data[name]['empty'];
			if(placeholder_msg == null){
				if(!empty){
					placeholder_msg = '请填写'+data[name]['describe'];
				}
			}
			
			if(placeholder_msg){
				if(!element.attr('placeholder')){
					element.attr('placeholder',placeholder_msg);
				}
			}
			
			element.attr('validate','true');
			var props = data[name];
			for(var prop in props){
				element.attr(prefix+prop,props[prop]);
			}
		}
		
		// 添加验证标记
		$('[validate]').each(function(){
			if($(this).attr(prefix+'js_mark') === 'false'){
				return;
			}
			//if(!$(this).attr('validate_marked')){
			//	$(this).attr('validate_marked','done');
			//	$('<div class="validate_mark">*</div>').insertAfter($(this));
			//}
		});
	}




	// 检查验证
	jq_validate.check_invalid = function (root){

		if(!root){
			root = $('body');
		}
		
		var prefix = 'vali_';
		
		// 验证不通过的对象集合
		var res_obj = {};
		
		root.find('[validate="true"]').each(function(){

			var one_invalid = false;
			function addto_one(name,add_msg){
				if(!res_obj[name]){
					res_obj[name] = [];
				}
				one_invalid = true;
				invalid = true;
				var arr = res_obj[name];
				if(!in_array()){
					arr.push(add_msg);
				}
				function in_array(){
					for(var index in arr){
						if(arr[index] == add_msg){
							return true;
						}
					}
					return false;
				}
			}
			var element = $(this);
			var describe = element.attr( prefix + 'describe' );
			var type = element.attr( prefix + 'type' );
			
			var value = element.val();
			
			
			var valid_empty = element.attr(prefix+'empty');
			if(valid_empty && value == ''){
				element.removeClass('check_invalid');
				return;
			}
			
			// 通配 text 校验
			if(value == ''){
				addto_one(describe,'为必填项');
			}
			
			// 长度校验
			var minlength = element.attr(prefix+'minlength');
			if(minlength){
				if(value.length < minlength){
					addto_one(describe,'长度不能小于 '+minlength);
				}
			}
			var maxlength = element.attr(prefix+'maxlength');
			if(maxlength){
				if(value.length > maxlength){
					addto_one(describe,'长度不能大于 '+maxlength);
				}
			}
			
			// 浮点数校验
			var floatlength = element.attr(prefix+'floatlength');
			if(floatlength){
				var reg_value = '^[0-9]+(.[0-9]{1,'+floatlength+'})?$';
				var reg = new RegExp(reg_value);
				if( !reg.test(value) ){
					addto_one(describe,'小数位数不能超过 '+floatlength+' 位');
				}
			}
			
			// 比较校验
			var less_than = element.attr(prefix+'less_than');
			if(less_than){
				var larger_element = root.find('[name="'+less_than+'"]');
				var larger_describe = larger_element.attr(prefix+'describe');
				var larger_value = larger_element.val();
				if( value > larger_value ){
					addto_one(describe,'不能大于 '+larger_describe);
				}
			}
			
			if(type){
				var type_info = jq_validate_valid_types[type];
				if(!type_info.valid.test(value)){
					addto_one(describe,'只能为 '+type_info.name);
				}
			}
			
			// 如果这个输入项验证不通过，加上不通过的样式
			if(one_invalid){
				element.addClass('check_invalid');
			}else{
				element.removeClass('check_invalid');
			}
		});
		
		
		// 处理验证结果
		var msg = '';
		for(var name in res_obj){
			var arr = res_obj[name];
			for(var index in arr){
				msg += name+'#th#'+arr[index]+'#td#';
			}
		}
		
		var invalid = msg != '';
		if(msg != ''){
			show_message(msg);
		}
		return invalid;
	}
})();
/*
 *	按钮切换状态jquery扩展
 *	2014-6-26-----------------
 *	根据checkbox的选择状态判断按钮可用性
 */
(function(){
	$.fn.switch_active = function(options){
		/*
			options:
				checkbox_name - 对应判断列表
				container - 外层范围元素
		*/
		var checkbox_name = options.checkbox_name;
		var container = options.container;
		if(!container){
			container = $('body');
		}
		
		container.on('change',':checkbox[name="'+checkbox_name+'"]',function(){
			check_avaliable();
		});
		
		var button = $(this);
		
		// 检查
		function check_avaliable(){
			var avaliable = false;
			container.find(':checkbox[name="'+checkbox_name+'"]').each(function(){
				if(avaliable){
					return;
				}
				if($(this).is(':checked')){
					avaliable = true;
				}
			});
			
			// 切换状态
			if(avaliable){
				button.removeAttr('disabled');
				button.removeClass('disabled_button');
			}else{
				button.attr('disabled','disabled');
				button.addClass('disabled_button');
			}

		}
		
		// 首次检测
		check_avaliable();
		
		// 提供对外接口，进行手动刷新判断（用于代码改变checkbox的checked属性时做判断）
		return {
			refresh : function(){
				check_avaliable();
			}
		};
	};
})();


// 
(function(){
	$.fn.formula_editor = function(options){
	
		var index_list = [];
		var variable_list = [];
		
		var formula_show_input = $(this);
		var formula_input = options.formula_input;
		var ids_input = options.ids_input;
		
		context_list = [
			{
				name : '指标',
				id:1,
				list : index_list
			},
			{
				name : '变量',
				id:2,
				list : variable_list
			}
		];
		
		var container = $(this);
		
		container.bind('input propertychange',function(){
			refresh_hdn();
		});
		
		// 右键菜单事件
		container.bind('contextmenu',function(event){
			var element = $(this);
			var position = GetPosition($(this)[0]);
			var value = element.val();
			var left_part = value.substring(0,position);
			var right_part = value.substring(position,value.length);
			
			context_menu(context_list,event.pageX,event.pageY,function(name,value){
				element.val(left_part + ' #'+name+'# '+right_part);
				refresh_hdn();
			});
			event.preventDefault();
		});
		
		function refresh_hdn(){
			var formula_show = formula_show_input.val();
			
			var ids = '';
			
			for(var index = 0 ; index < index_list.length ; index++){
				var each = index_list[index];
				var name = each.name;
				var code = each.value;
				var v_uid = each.v_uid;
				if(formula_show.indexOf('#'+name+'#') != -1 && ids.indexOf(','+v_uid+'#') == -1){
					ids += ','+v_uid+'#';
				}
				formula_show = formula_show.replace(new RegExp('#'+name+'#','g'),code);
			}
			
			for(var index = 0 ; index < variable_list.length ; index++){
				var each = variable_list[index];
				var name = each.name;
				var code = each.value;
				var v_uid = each.v_uid;
				if(formula_show.indexOf('#'+name+'#') != -1 && ids.indexOf(','+v_uid+'#') == -1){
					ids += ','+v_uid+'#';
				}
				formula_show = formula_show.replace(new RegExp('#'+name+'#','g'), code);
			}
			formula_input.val(formula_show);
			if(ids != ''){
				ids = ids.substring(1,ids.length);
			}
			ids_input.val(ids);
		}
		
		function refresh_formula(){
			var formula = formula_input.val();
			
			for(var index = 0 ; index < index_list.length ; index++){
				var each = index_list[index];
				var name = each.name;
				var code = each.value;
				formula = formula.replace(new RegExp(code,'g'),'#'+name+'#');
			}
			
			for(var index = 0 ; index < variable_list.length ; index++){
				var each = variable_list[index];
				var name = each.name;
				var code = each.value;
				formula = formula.replace(new RegExp(code,'g'),'#'+name+'#');
			}
			
			formula_show_input.val(formula);
			refresh_hdn();
			
		}
		
		function GetPosition(input)
		{
			if($.browser.msie){
				var cuRange=document.selection.createRange();
				var tbRange=input.createTextRange();
				tbRange.collapse(true);
				tbRange.select();
				var headRange=document.selection.createRange();
				headRange.setEndPoint("EndToEnd",cuRange);
				var pos=headRange.text.length;
				cuRange.select();
				return pos;
			}
			else{
				return input.selectionStart;
			}
		}
		

		
		return {
			set_index_list : function(list){
				index_list = list;
				context_list[0].list = index_list;
			},
			set_variable_list : function(list){
				variable_list = list;
				context_list[1].list = variable_list;
			},
			refresh_formula : function(){
				refresh_formula();
			}
		};
	};
	
})();


$.fn.set_orderable_table = function(options){

	var order_list = [];

	if(!options.form_id){
		options.form_id = 'theForm';
	}
	
	if(!options.search_func){
		options.search_func = search_frist;
	}
	
	var form = $('#'+options.form_id);
	var table = $(this);
	if(form.find('input[type="hidden"][name="order_column"]').length == 0){
		form.prepend($('<input type="hidden" name="order_column" />'));
	}
	table.find('th[order_column]').each(function(){
		var th = $(this);
		th.addClass('orderable_table_th');
		var mark = $('<div class="orderable_table_mark"></div>');
		var default_type = th.attr('order_default');
		var text = th.text();
		var container = $('<div class="orderable_table_th_container">'+text+'</div>');
		if(default_type){
			th.attr('order_status',default_type);
			var column_name = th.attr('order_column');
			mark.addClass('orderable_table_mark_'+default_type);
			/*
			 *	多列排序预留
			 */
			order_list = [{
				column:column_name,
				order:default_type
			}];
			change_orderby(th);
		}else{
			th.attr('order_status','normal');
			mark.addClass('orderable_table_mark_normal');
		}
		th.text('');
		th.append(container);
		container.append(mark);
		th.bind('click',function(event){
			var is_add = event.shiftKey;
			switch_status($(this),is_add);
		});
	});
	
	function switch_status(th,is_add){
		var status = th.attr('order_status');
		var column_name = th.attr('order_column');
		var next_status = null;
		switch(status){
			case 'normal':
			case 'desc':
				next_status = 'asc';
			break;
			case 'asc':
				next_status = 'desc';
			break;
		}
		
		th.attr('order_status',next_status);
		var mark = th.find('.orderable_table_mark');
		mark.removeClass('orderable_table_mark_'+status);
		mark.addClass('orderable_table_mark_'+next_status);
		
		if(!is_add){
			table.find('th[order_column][order_column!="'+column_name+'"]').each(function(){
				var element = $(this);
				element.attr('order_status','normal');
				var mark = element.find('.orderable_table_mark');
				mark.removeClass('orderable_table_mark_asc');
				mark.removeClass('orderable_table_mark_desc');
				mark.addClass('orderable_table_mark_normal');
			});
			order_list = [{
				column:column_name,
				order:next_status
			}];
		}else{
			var is_in_order = false;
			for(var index = 0 ; index < order_list.length ; index++){
				if(order_list[index].column == column_name){
					order_list[index].order = next_status;
					is_in_order = true;
				}
			}
			if(!is_in_order){
				order_list.push({
					column:column_name,
					order:next_status
				});
			}
		}
		
		change_orderby();
		options.search_func();
		
	}
	
	function change_orderby(){
		var columns_str = '';
		
		for(var index = 0 ; index < order_list.length ; index++){
			var column = order_list[index].column;
			var order = order_list[index].order;
			if(index == 0){
				columns_str += column+' '+order;
			}else{
				columns_str += ','+column+' '+order;
			}
		}
		$('[name="order_column"]').val(columns_str);
	}
	
}