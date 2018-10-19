(function(jQuery) {

//加载数据方法

jQuery.personSearchMultiple = function(options){
   
    var options = jQuery.extend({
	   single: true,
	   spanid:"user_list",
	   spanshow:"user_list_show",
       complate: function(data){}
	}, options);
    
    init();//初始化页面
    function init(){
    	 //判断是否有节点        
		var person_str = $('#person_dialogMultiple');
		/*
			重写人员搜索，默认查出所有人员，用js进行快速筛选_zhouxiang_2014-7-1
			原select[multiple="true"]>option:hidden在浏览器兼容性上完全无法实现，故左侧换用div模拟select :)
		*/
		if (person_str.length == 0)
		{	  var html = $('    	<div id="person_dialogMultiple" title="人员选择" style="display:none">'
							+ '		<table style="table-layout:fixed;border-collapse:collapse;cellspacing:0px;cellpadding:0px;margin-left:55px;margin-top:0px;">'
							+ '			<tr>'
							+ ' 			<td id="leftPerson_span" style="padding:0px;margin-left:60px;width:230px;"> '
							+ '					<div style="line-height:40px;">可选人员：</div>'
							+ '					<input id="fast_filter_input" placeholder="输入姓名、工号、邮箱快速筛选" style="border:1px solid gray;width:225px;padding:0px;padding-left:5px;height:25px;line-height:25px;display:block;" />'
							+ ' 				<div id="leftPerson" class="select_list_person" style="margin-top:-1px;border:1px solid gray;padding:0px;width:230px;height:304px;background-color:#FFFFFF" >'
							+ ' 				</div>'
							+ ' 			</td>'
							+ ' 			<td id="PersonBtn_span" style="width:100px;">'
							+ ' 				<span><input type="button" id="rightPersonBtn" style="margin-left:25px;width:50px;" value=">" /></span><br/>'
							+ ' 				<span><input type="button" id="leftPersonBtn" style="margin-top:20px;margin-left:25px;width:50px;" value="<" /></span>'
							+ ' 			</td>'
							+ ' 			<td id="rightPerson_span" style="width:230px;">'
							+ '					<div style="line-height:40px;">已选人员：</div>'
							+ ' 				<select id="rightPerson" style="border:1px solid gray;width:100%;height:330px;background-color:#FFFFFF" MULTIPLE= "true">'
							+ ' 				</select>'
							+ ' 			</td>'
							+ ' 		</tr>'
							+ ' 	</table>'
							+ '	</div>');
				
				$('body').append(html);
				$("#rightPersonBtn").click(function(){
					$("#leftPerson div.select_list_left_selected").each(function(){
						var value = $(this).attr('value');
						var text = $(this).text();
						var option = $('<option value="'+value+'">'+text+'</option>');
						option.appendTo('#rightPerson');
						$(this).detach();
					});
				});
				$("#leftPersonBtn").click(function(){
					$("#rightPerson option:selected").each(function(){
						var value = $(this).attr('value');
						var text = $(this).text();
						var option = $('<div class="select_list_left" value="'+value+'">'+text+'</div>');
						option.appendTo("#leftPerson");
						$(this).detach();
					});
				});
				$("#fast_filter_input").bind('propertychange input',function(){
					getPerson($(this).val());
				});
				$(document).on('click','.select_list_left',function(){
				var element = $(this);
				if(element.hasClass('select_list_left_selected')){
					element.removeClass('select_list_left_selected');
				}else{
					element.addClass('select_list_left_selected');
				}
		 });
		}else{
		 	$("#leftPerson").empty();
		 	$("#fieldMultiple").val("");
		}
		 $("#person_dialogMultiple").dialog({
          resizable:false,
          width:700,
		  height:500,
		  modal: true,
          buttons: {
	         '取消': function() {
				$(this).dialog('close');
	          },
	         '确定': function() { 
	        	 
	        	  var persons = "";
	        	  var persons_show = "";
	        	  $("#rightPerson option").each(function(){
	        		  persons += $(this).val()+",";
	        		  persons_show += $(this).text()+",";
	        	  });
	        	  persons = persons.substring(0,(persons.length-1));
	        	  persons_show = persons_show.substring(0,(persons_show.length-1));
	        	  var obj = new Object();
				  obj.persons = persons;
				  obj.persons_show = persons_show;
	        	  options.complate(obj);
	        	  $(this).dialog('close');
	         }
		   }
         });
		 initRightPerson();
		 getAllPerson();
    }
    /**
     * 查询人员列表
     */
    function getPerson(value){
    	$.ajax({
    		type:"POST",
			dataType:"html",
			data:"q="+value,
			url:"index.php/tools/person/getPerson",
			success: function(data) {
    			var data_list = JSON.parse(data);
				var leftOption = "";
				//排除右侧已选中
				var selectedPerson = $("#"+options.spanid).val();
				var selectedPersonList = selectedPerson.split(",");
				for(var i=0;i<data_list.length;i++){
					//已在右侧选中的，左侧将不再列出
					var selected = false;
					$("#rightPerson option").each(function(){
						if(data_list[i].salarysn==$(this).val()){
							selected = true;
						}
					});
					if(!selected){
						var item = data_list[i];
						var value = item.salarysn;
						var mail = item.email;
						var show = item.name+"&nbsp;("+item.salarysn+")";
						if(mail != ''){
							mail = mail.substring(0,mail.indexOf('@'));
							show = item.name+"("+item.salarysn+"&nbsp;"+mail+")";
						}
						leftOption +="<div class='select_list_left' value='"+value+"'>"+show+"</div>";
					}
				}
				$("#leftPerson").empty();
				$("#leftPerson").append(leftOption);  
			}
		});
    	
    }
    /**
     * 查询所有人员列表
     */
    function getAllPerson(){
    	$.ajax({
    		type:"POST",
			dataType:"html",
			data:"q="+$('#fieldMultiple').val(),
			url:"index.php/tools/person/get_all_person",
			success: function(data) {
    			var data_list = JSON.parse(data);
				var leftOption = "";
				//排除右侧已选中
				var selectedPerson = $("#"+options.spanid).val();
				var selectedPersonList = selectedPerson.split(",");
				for(var i=0;i<data_list.length;i++){
					//已在右侧选中的，左侧将不再列出
					var selected = false;
					$("#rightPerson option").each(function(){
						if(data_list[i].salarysn==$(this).val()){
							selected = true;
						}
					});
					if(!selected){
						var item = data_list[i];
						var value = item.salarysn;
						var mail = item.email;
						var show = item.name+"&nbsp;("+item.salarysn+")";
						if(mail != ''){
							mail = mail.substring(0,mail.indexOf('@'));
							show = item.name+"("+item.salarysn+"&nbsp;"+mail+")";
						}
						leftOption +="<div class='select_list_left' value='"+value+"'>"+show+"</div>";
					}
				}
				$("#leftPerson").empty();
				$("#leftPerson").append(leftOption);  
			}
		});
    	
    }
     //初始化已选中
    function initRightPerson(){
		var rightOption = "";
		//右侧已选中
		var selectedPerson = $("#"+options.spanid).val();
		var selectedPersonList = "";
		$.ajax({
			url:"index.php/tools/person/checkPerson",
			type:"POST",
			async:false,
			data:"names="+selectedPerson,
			dataType:"json",
			success:function(data){
				selectedPersonList = data;
			}
		});
		for(var i=0;i<selectedPersonList.length;i++){
			if(selectedPersonList[i]!=""){
				rightOption += "<option value='"+selectedPersonList[i].value+"'>"+selectedPersonList[i].usershow+"</option>";
			}
		}
		$("#rightPerson").empty();
		$("#rightPerson").append(rightOption);
    }
    
}

})(jQuery);
