
(function(jQuery) {

jQuery.personSearchmemo = function(options){
   
    var options = jQuery.extend({
	   single: true,
	   baseurl:"",
	   memo:false,
       complate: function(data){}
	}, options);
    
    init();
    autocomplete();
	/**
	 * 选人窗口页面
	 */
	function init(){
		var html = "";
		html += '<div id="personSearchmemo_div" style="height:600px;display:none" >';
		if(options.memo){
			html +=	'<form class="form-inline">';
			html += '<label class="col-sm-2 control-label">会签意见:</label>'
			html += '<div class="col-sm-10"><textarea class="form-control" id="huiqian_memo"></textarea>'
			html +=	'</div>';
			html +=	'</form>';
		}
		html +=	'<form class="form-inline">';
		html += '  <label class="col-sm-2 control-label">选择人员:</label>'
		html += '  <div class="col-sm-10">';
		html += '  <div class="input-group">';
		html +=	'  	  <input type="text" placeholder="员工工号/姓名/邮箱"  class="form-control" id="personSearchmemo_username" />';
		html +=	'     <span class="input-group-btn">';
		html +=	'  	  <button id="personSearchmemo_button" type="button" class="btn btn-primary btn-sm" >搜索</button>';
		html +=	'     </span>';
		html +=	'  </div>';
		html +=	'  </div>';
		html +=	'</form>';
		html +=	'<table  class="table table-striped" >';
		html +=	'<thead>';
		html += '<tr>'
		html += '<th></th>';
		html += '<th>工号</th>	';
		html += '<th>姓名</th>';
		html += '<th>部门</th>';
		html += '<th>邮箱</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody id="person_content" >';
		html += '</tbody>';
		html +=	'</table>';

		html += '</div>';
		
		var person_str = $('#personSearchmemo_div');
		if (person_str.length == 0){
			$('body').append(html);
		}	
		//绑定搜索按钮
		$('#personSearchmemo_button').click(function(){
			personSearchmemo_query();
		});
	    $("#personSearchmemo_div").dialog({
			title:"选择人员",
			resizable:false,
			width:700,
			height:500,
		    modal: true,
			buttons: {
               
                '确定': function() {
	    	 				var obj = new Object();
					    	$('input[name=salarysn]:radio').each(function(){
				                if (this.checked)
				                {
									 obj.salarysn = $(this).val();
									 obj.name = $(this).parents("tr").find("td:eq(2)").text();
									 obj.deptname = $(this).parents("tr").find("td:eq(3)").text();
									 obj.email = $(this).parents("tr").find("td:eq(4)").text();
									 obj.memo = $("#huiqian_memo").val();
							         options.complate(obj);
				                  }
								});
					    	if(obj.salarysn == undefined){
					    		layer.alert("请选择人员！");
					    		return;
					    	}
					    	if(options.memo==true && $.trim(obj.memo)==''){
					    		layer.alert("请填写会签意见！");
					    		return;
					    	}
						    $(this).dialog('close');
                        },
				 '取消': function() {
                            $(this).dialog('close');
                        }
			}
		});
	}
	/**
	 * 自动提示
	 */
	function autocomplete(){

		var searchUrl = options.baseurl+"index.php/tools/person/getPerson";
		$("#personSearchmemo_username").autocomplete({
			source: function(request, response) { 
				$.ajax({ 
					url: searchUrl, 
					dataType: "json", 
					data:"q="+encodeURIComponent($('#personSearchmemo_username').val()),
					success: function( data ) { 
						response( $.map( data, function( item ) {
						return {
							label: item.name ,
							value: item.name
						}
					}));
					} 
				}); 
			},
			minLength: 1 
		});
		
	}
	/**
	 * 按人员搜索
	 */
	function personSearchmemo_query(){
		
		$.ajax({
			url:options.baseurl+"index.php/tools/person/getPerson",
			dataType:"json",
			type:"POST",
			data:"q="+$("#personSearchmemo_username").val(),
			success:function(data){
				var html = "";
				$(data).each(function(){
					html += "<tr>";
					html += "<td nowrap style='width:15px;'><input type='radio' value='"+this.salarysn+"' name='salarysn' /></td>";
					html += "<td nowrap>"+this.salarysn+"</td>";
					html += "<td nowrap>"+this.name+"</td>";
					html += "<td >"+this.organization+"</td>";
					html += "<td nowrap>"+this.email+"</td>";
					html += "</tr>";
				});
				$("#person_content").html(html);
			}
		});
	}

}

})(jQuery);