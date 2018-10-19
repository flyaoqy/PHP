

(function(){

	/*	
		2013-10-10_zhouxiang 
		控件封装
		
		2014-7-21_zhouxiang 
		简化原封装，提高灵活性，添加了子列显示的封装
		
		2014-8-4_zhouxiang
		重写datatable的横向滚动，抛弃原有的分离表格头和表格体方式，原因：样式不可控制
		（注：换用dom选项<"class"t>解决）
		
	*/
	window.dataTableHelper = new Object();

	// 初始化控件
	$.fn.initDataTable = function(aoConfig){
		
		/*
			除原aoConfig补充配置：
			1、finish_callback - function - ajax请求得到响应后回调函数
			2、open_url - string - 单据打开的地址
			3、select_all_btn - selector - 全选按钮
		*/
		
		var table = $(this).find('table[datatable]');
		var conditions = $(this).find('div[dt_search_information]');
		var form = $(this).find('form[dt_form]');
		
		var open_url = aoConfig.open_url;
		
		var jq_block = null;
		
		var basicConfig = {
			'bLengthChange':true,
			'iDisplayLength':20,
			"aLengthMenu": [[10, 20, 50], ["10 ", "20 ", "50 "]],
			'bServerSide': true,
			"bAutoWidth": false,
			'bFilter' : false,
			//"sScrollX": "100%",
			//"dom": '<"datatable_scroll_container"t><"bottom"i<"datatable_length_container"l>p><"clear">',
			"dom": '<"datatable_scroll_container"t><"bottom"<"datatable_length_container">p><"clear">',
			'fnServerData': function ( sSource, aoData, fnCallback ) {
			
				jq_block = $.jq_block();
			
				// 在这里补充name-value的Object给aoData进行传递（搜索条件）
				conditions.find('select,input').each(function(){
					if($(this).is(':checkbox') || $(this).is(':radio')){
						if(!$(this).is(':checked')){
							return;
						}
					}
					var eachData = new Object();
					eachData.name = $(this).attr('name');
					eachData.value = $(this).val();
					aoData.push(eachData);
				});
				
				$.ajax( {
					'dataType': 'json',
					'type': 'POST',
					'url': sSource,
					'data': aoData,
					'success': function(data,status){
						fnCallback(data,status);
						jq_block.close();
						// 去掉全选按钮的勾
						if(aoConfig.select_all_btn){
							aoConfig.select_all_btn.removeAttr('checked');
						}else{
							table.find('[name="chkall"]').removeAttr('checked');
						}
						// 增加一个自定义的回调函数
						if(aoConfig.finish_callback){
							aoConfig.finish_callback(data);
						}
					}
				} );
			},
			sPaginationType: 'full_numbers',
			oLanguage:{
				sInfo:'共_TOTAL_条记录',
				oPaginate:{
					sPrevious:'上一页',
					sNext:'下一页',
					sFirst:'首页',
					sLast:'尾页'
				},
				sZeroRecords:'没有记录',
				sInfoEmpty:'共0条记录',
				sSearch:'搜索：',
				sLengthMenu: '每页显示 _MENU_ 条记录'
			}
		};
		
		// 将预定义属性与自定义属性整合
		for(var prop in aoConfig){
			basicConfig[prop] = aoConfig[prop];
		}
		
		var oTable = table.DataTable(basicConfig);
		
		
		if(open_url){
			table.addClass('dt_row_pointer');
			table.on('click','tr',function(){
				var row = oTable.row($(this)).data();
				switch(typeof(open_url)){
					case 'function':
						open_url(row);
					break;
					case 'string':
						var id = row.dt_primary_id;
						window.open(open_url+id,'_blank');
					break;
				}
			});
		}
		if(aoConfig.row_detail){
			var detail = aoConfig.row_detail;
			var class_name = detail.class_name;
			var detail_func = detail.format_func;
			$(this).on('click', '.'+class_name , function () {
				var tr = $(this).closest('tr');
				var row = oTable.row( tr );
				if ( row.child.isShown() ) {
					row.child.hide();
					tr.removeClass('shown');
				}
				else {
					row.child( detail_func(row.data()) ).show();
					tr.addClass('shown');
				}
			});
		}
		
		// 导出excel
		oTable.export_excel = function(){
			var order = get_order_by();
			form.find('[name="excel_orderby"]').val(order);
			form[0].submit();
			
			// 获取orderby 用于excel导出
			function get_order_by(){
				var params = oTable.ajax.params();
				var count = params['iSortingCols'];
				var orderby = ' order by ';
				var columns_arr = params['sColumns'].split(',');
				for( var i = 0 ; i < count ; i++){
					var i_sort_col = params['iSortCol_'+i];
					var s_sort_dir = params['sSortDir_'+i];
					orderby += (i == 0?'':',')+columns_arr[i_sort_col]+' '+s_sort_dir;
				}
				var tail_orderby = form.find('[name="tail_orderby"]').val();
				if(tail_orderby){
					orderby += ','+tail_orderby+' desc';
				}
				return orderby;
			}
			
		}
		
		return oTable;
	};
	
	// fnRender 工具
	dataTableHelper.render = new Object();
	// 金额格式化
	dataTableHelper.render.number_format = function(money){
		return FormatNumber(money,2);
	};
	// 整数格式化
	dataTableHelper.render.cnt_format = function(cnt){
		return FormatNumber(cnt,0);
	};
	// 勾选id列初始化
	dataTableHelper.render.id_format = function(id,name){
		return '<input onclick="event.cancelBubble = true;" type="checkbox" name="'+name+'" value="'+id+'" />';
	};
	// 链接初始化
	dataTableHelper.render.a_format = function(id,url){
		return '<a style="text-decoration:none;"  target="blank" href="javascript:void()" onclick="open_url(\''+url+'\')">'+id+'</a>';
	};
	// 根据id的有无显示复选框
	dataTableHelper.render.id_format_display = function(id,name){
		if(id != ""){
			return '<input onclick="event.cancelBubble = true;" type="checkbox" name="'+name+'" value="'+id+'" />';
		}else{
			return '';
		}
	};
	
	dataTableHelper.render.id_format_title = function(id,name,status){
		if(status == "未提交" || status == "已驳回"){
			return '<div class="details-control"  title = "修改奖励金额"></div>';   
		}else{
			return '';
		}
	};
	//根据状态判断显示‘提交’、‘通过’，‘驳回’
	dataTableHelper.render.id_format_doAction = function(id,name,rw_sn){
		if(id != ""){
			var strs=id.split(";"); //字符分割 
			var str_id = strs[0];
			var status = strs[1];
			var lable = strs[2];
			if(status == "草稿" || status == "驳回"){
				return '<div onclick="event.cancelBubble = true;"><a style="margin-left: 20px" href="javascript:void(0)" onclick=doAction("'+str_id+'","0","'+rw_sn+'","提交")>提交</a></div>';
			}else if(status == "流程中" && lable == "NM"){
				return '<div onclick="event.cancelBubble = true;"><a style="margin-left: 10px" href="javascript:void(0)" onclick=doAction("'+str_id+'","11","'+rw_sn+'","通过")>通过</a><a style="margin-left: 20px" href="javascript:void(0)" onclick=doAction("'+str_id+'","-100","'+rw_sn+'","驳回")>驳回</a></div>';
			}else{
				return '';
			}
		}else{
			return '';
		}
	};
	
	// 控制编辑图标的显隐
	dataTableHelper.render.id_edit_display = function(id,name){
		if(id == ""){
			return '<div id="test" style="display: none;" class="details-control"></div>';
		}else{
			return '';
		}
	};
	// 字符长度限制
	dataTableHelper.render.length_limit = function(value,max){
		var title = value;
		if(value.length > max){
			value = value.substring(0,max)+'...';
		}
		return '<span title="'+title+'">'+value+'</span>';
	};
	//宽度限制
	dataTableHelper.render.width_fixed = function(value,width){
		return '<span class="dt_width_fixed" style="width:'+width+'" title="'+value+'">'+value+'</span>';
	};
})();
