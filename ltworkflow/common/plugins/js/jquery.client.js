(function(jQuery) {

//实物物品查询

jQuery.clientSearch = function(options){
   
    var options = jQuery.extend({
	   single: true,
	   width: 800,
	   height: 500,
	   url : "index.php?/tools/client/getClient",
       complate: function(data){}
	}, options);
    
    init();
    function init(){
    	options.bodyHeader = 
    		'<div id="cilent_div" style="display:none">'
    		+'<div class="container-fluid" id="datatable_container"  > '
			+'	<div class="search-condition" dt_search_information>'
			+'		<div class="row">'
			+'			<div class="col-md-4 col-lg-4">'
			+'			<input type="text" class="form-control" name="client_name" placeholder="客户名称">'
			+'			</div>'
			+'			<div class="col-md-4 col-lg-4">'
			+'			<input type="text" class="form-control" name="author"  placeholder="创建人">'
			+'			</div>'	
			+'			<div class="col-md-4 col-lg-4">'
			+'			<button type="button" id="search_btn" class="btn btn-primary btn-sm">搜索</button>'
			+'			</div>'
			+'		</div>'
			+'	</div>'				
			+'	<div class="search_list" style="margin-top: 20px">'
			+'		<table datatable style="text-align: center;overflow: auto; box-sizing: border-box">'
			+'		<thead>'
			+'		<tr>'
			+'			<th></th>'
			+'			<th nowrap>选择</th>'
			+'			<th nowrap>名称</th>'
			+'			<th nowrap>类型</th>'
			+'			<th nowrap>创建人</th>'
			+'			<th nowrap>uid</th>'
			+'		</tr>'
			+'		</thead>'
			+'		<tbody id="content">'
			+'		</tbody>'
			+'		</table>'
			+'	</div>'
			+'<form id="formHidden" name="formHidden" action="" method="post">'
			+'</form>'
			+'</div>'
			+'</div>';


             //判断是否有节点        
			var jsupplyer = $('#cilent_div');
			 jsupplyer.remove();
				
			$('body').append(options.bodyHeader);

            loadData();
            
			$("#cilent_div").dialog({
	          resizable:false,
	          width:options.width,
			  height:options.height,
			  modal: true,
	          buttons: {
		         '关闭': function() {

					$(this).dialog('close');

		          },
		         '确定': function() { 

                      var id = "";
                      var showname = "";
                       $('input[name=sel_client]:radio').each(function(){
                         if (this.checked)
                         {
							 id = $(this).val();
							 showname = $(this).attr('showname');
                         }
					   });
                       var obj = new Object();
                       obj.id = id;
                       obj.showname = showname
                       options.complate(obj); 
				      $(this).dialog('close');
		         }
			   }
              });

		
    }
    /**
     * 加载数据
     */
    function loadData()
	{
    	// 此方法调用在function.js，对原始方法进行了封装，可以重复定义已封装属性进行覆盖
    	window.myTable = $('#datatable_container').initDataTable({
    		'sAjaxSource':options.url,
    		'iDisplayLength':10,
    		'order': [2] ,
    		'columns':[
    			{
    				data:'dt_primary_id',
    				orderable:false,
    				bVisible:false
    			},
    			{
    				orderable:false,
    				render:function(data,type,row){
    					var str = '<input type="radio" name="sel_client" showname="'+row['clit_showname']+'" value="'+row['dt_primary_id']+'" />'
                    	return str;
                	}
    			},
    			{sName:'clit_showname',data:'clit_showname',className:'dt_nowrap'},
    			{sName:'type',data:'type',className:'dt_nowrap'},
    			{sName:'name',data:'name',className:'dt_nowrap'},
    			{sName:'dt_primary_id',data:'dt_primary_id',className:'dt_nowrap'}
    			
    		]
    	});
    	
    	$('#search_btn').bind('click',function(){
    		window.myTable.ajax.reload();
    	});
	}

}

})(jQuery);
