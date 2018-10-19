
//-------------------------------------------------------

var demo = {
	/**
	 * 保存
	 */
	doAction:function (action_id){
		//会签处理
	    if (action_id == '-500' || action_id == '-550') {
	        $.personSearchmemo({
	        	memo:true,
	            baseurl: formGlobal.baseurl,
	            complate: function (data) {
	                $(data).each(function () {
	                    $('#__router').val(this.salarysn);
	                    $('#__comm').val(this.memo);
	                    __doAction(action_id); 
	                });
	            }
	        });
	    }else{//提交
	    	__doAction(action_id); 
	    }
		
	},
	/**
	 * 选单个人
	 */
	select_person:function (){
		$.personSearchmemo({
			single: true,
			baseurl:formGlobal.baseurl,
			complate: function(data){
				var salarysn = data.salarysn;
				var name = data.name;	
				$("#signer_sn").val(salarysn);
				$("#signer_name").val(name);
			}
		}); 

	},
	/**
	 * 流程预览
	 */
	flowPreview:function (et_uid) {
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
		    content: 'crm.php/config/simulate/draw_flow?etuid=' + et_uid
		});
	},
	/**
	 * 当前审批人
	 */
	get_approver:function(obj,etuid){
		var url = formGlobal.baseurl+"crm.php/tools/wf_interface/get_approver";
		$.ajax({     
	        url: url,    
	        type: 'post',     
	        data: 'etuid=' + etuid,
	        dataType: "json",   
	        async: true, //默认为true 异步     
	        error: function(){     
	        	layer.alert("ERROR!", 8, !1);   
	        },     
	        success: function(data){
	        	var users = "";
	        	var nodename = "";
	        	$(data).each(function(){
	        		nodename = this.cs_nodename;
	        		users += this.name+",";
	            });
	        	if(users != ""){
	        		var content = nodename +"："+ users;
	        		layer.tips(content, obj,
		        			{tips: [2,'#8ECAF7'], time: 2000}
		        			);
	        	}
	        	
	        }
	    });
		
		
	}
	
};
