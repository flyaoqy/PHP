<div class="container-fluid" style="border: 1px solid #2979b4;padding:50px 20px 40px 20px">
<div class="row" style="text-align:right">
    <div class="col-md-offset-7 col-sm-5 col-sm-offset-7 col-xs-6 col-xs-offset-6 ">
    	<button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="route();">重新路由
    	</button>
        <button type="button" id="submitbutton" class="btn btn-primary btn-sm" onclick="window.history.go(-1);" >返回
        </button>
    </div>
</div>

<form class="form-horizontal" style="margin-bottom:3px;" role="form" id="theForm" method="post" action="">
	 <input type="hidden" id="wf_stepid" value=""/>
     <input type="hidden" id="wf_wfid" value=""/>
     <input type="hidden" id="wf_user" value=""/>
     <input type="hidden" id="wf_deptid" value=""/>
	<div class="back_title">
    <h2>单据信息</h2>
	</div>
	<div class="form-group form-group-sm" style="margin-top: 20px">

		<div class="row">
			<label class="col-sm-2 col-xs-2 control-label input-sm">单据编号 / etuid：</label>
			 <div class="col-sm-4 col-xs-4">
	        	<input type="text"  class="form-control input-sm" name="etuid" id="etuid"/>
	    	 </div>
	    	 <button type="button" class="btn btn-xs btn-primary"  onclick="lookup()">获取单据信息</button>
		</div>
	</div>
	<div class="back_title">
    <h2>调整流程</h2>
	</div>
	<div class="form-group form-group-sm" style="margin-top: 20px">

		<div class="row">
			<label class="col-sm-2 col-xs-2 control-label input-sm">调整方式：</label>
			 <div class="col-sm-6 col-xs-6">
                    <input type="radio" class="radio-input" name="change_type" value="byuser" checked
                           onclick="div_show()">
                    	调整至审批人（by审批记录）
                    <input type="radio" class="radio-input" name="change_type" value="bystep" onclick="div_show()">
                   	 调整至环节（by流程配置）
	    	 </div>
		</div>
		<div id="div_user"  class="row">
			<label class="col-sm-2 col-xs-2 control-label input-sm">选择审批人：</label>
			<div class="col-sm-4 col-xs-4">
	        	 <select id="chooseuser" style="min-width:250px;" ></select>
	    	</div>
		</div>
		<div id="div_step"  class="row">
			<label class="col-sm-2 col-xs-2 control-label input-sm">选择审批环节：</label>
			<div class="col-sm-4 col-xs-4">
	        	<select id="choosestep" style="min-width:250px;" ></select>
	    	</div>
		</div>
	</div>
	<div class="back_title">
	<h2>流程信息</h2>
	</div>
		<h5>当前环节表</h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th nowrap>当前节点</th>
					<th nowrap>审批人</th>
					<th nowrap>工资号</th>
					<th nowrap>状态</th>
					<th nowrap>流程名称</th>
				</tr>
			</thead>
			<tbody id="current_list">
				<tr>
					<td colspan="100%"></td>
				</tr>
			</tbody>
		</table>
		<h5>审批日志表</h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th nowrap>审批人</th>
					<th nowrap>工资号</th>
					<th nowrap>审批环节</th>
					<th nowrap>审批操作</th>
				</tr>
			</thead>
			<tbody id="log_list">
				<tr>
					<td colspan="100%"></td>
				</tr>
			</tbody>
		</table>
		
		<h5>单据流程表</h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th nowrap>当前流程</th>
					<th nowrap>流程uid</th>
					<th nowrap>单据etuid</th>
				</tr>
			</thead>
			<tbody id="workflow_list">
				<tr>
					<td colspan="100%"></td>
				</tr>
			</tbody>
		</table>
</form>
</div>

<div id="dialog_div" style="display:none">
</div>

<script type='text/JavaScript'>
    var baseurl = "<?php echo base_url()?>";
    $(function () {
        div_show();
    });

    //选择项显隐控制
    function div_show() {
        var ckvalue = $("input[name='change_type']:checked").val();
        $("#div_user,#div_step,#div_dept").hide();
        if (ckvalue == 'byuser') {
            $("#div_user").show();
        } else if (ckvalue == 'bystep') {
            $("#div_step").show();
        } else if (ckvalue == 'byorg') {
            $("#div_dept").show();
        }
    }

    //查看当前单据流程状态
    function lookup() {
        var id = $("#etuid").val();
        if (id == '') {
            alert("请填写单据id");
            return;
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseurl + "index.php/config/route/query",
            data: "etuid=" + id,
            success: function (result) {
                //当前环节
                var currenttab = "";
                var wfcurrent = result['wfcurrent'];
                if (wfcurrent.length == 0) {
                    currenttab = "<tr><td colspan='100%'>流程已完成</td></tr>"
                } else {
                    for (var i = 0; i < wfcurrent.length; i++) {
                        currenttab += "<tr>";
                        currenttab += "<td>" + wfcurrent[i].cs_nodename + "</td>";
                        currenttab += "<td>" + wfcurrent[i].u_name + "</td>";
                        currenttab += "<td>" + wfcurrent[i].u_usercode + "</td>";
                        currenttab += "<td>" + wfcurrent[i].status + "</td>";
                        currenttab += "<td>" + wfcurrent[i].wf_name + "</td>";
                        currenttab += "</tr>";
                    }
                }
                $("#current_list").html(currenttab);
                //流程信息
                var wflist = result['wflist'];
                var workflowtab = "";
                if (wflist.length == 0) {
                    workflowtab = "<tr><td colspan='100%'>无流程信息！</td></tr>"
                } else {
                    for (var i = 0; i < wflist.length; i++) {
                        workflowtab += "<tr>";
                        workflowtab += "<td>" + wflist[i].wf_name + "</td>";
                        workflowtab += "<td>" + wflist[i].wf_uid + "</td>";
                        workflowtab += "<td>" + wflist[i].et_uid + "</td>";
                        workflowtab += "</tr>";
                    }
                }
                $("#workflow_list").html(workflowtab);
                //审批日志
                var logtab = "";
                var loglist = result['wflog'];
                if (loglist.length == 0) {
                    workflowtab = "<tr><td colspan='100%'>无审批日志！</td></tr>"
                } else {
                    for (var i = 0; i < loglist.length; i++) {
                        logtab += "<tr>";
                        logtab += "<td>" + loglist[i].u_name + "</td>";
                        logtab += "<td>" + loglist[i].u_usercode + "</td>";
                        logtab += "<td>" + loglist[i].wf_stepname + "</td>";
                        logtab += "<td>" + loglist[i].status + "</td>";
                        logtab += "</tr>";
                    }
                }
                $("#log_list").html(logtab);
                //清空列表
                $("#chooseuser,#choosestep,#choosedept").html("");
                //人员列表
                var users = result['usrlist'];
                for (var i = 0; i < users.length; i++) {
                    var optionHtml = "<option stepid='" + users[i].cs_id + "' wfid='" + users[i].wf_uid + "' user='" + users[i].u_usercode + "' nodename='" + users[i].wf_stepname + "'>" + users[i].u_name + "[" + users[i].wf_stepname + "]</option>";
                    $("#chooseuser").append(optionHtml);
                }
                //环节列表
                var steps = result['steplist'];
                for (var i = 0; i < steps.length; i++) {
                    var optionHtml = "<option stepid='" + steps[i].stpeid + "' wfid='" + steps[i].wfid + "' nodename='" + steps[i].stpename + "'>" + "[" + steps[i].wfname + "]" + steps[i].stpename + "</option>";
                    $("#choosestep").append(optionHtml);
                }
            }
        });
    }

    //路由
    function route() {
        var id = $("#etuid").val();
        if (id == '') {
            alert("请填写单据id");
            return;
        }
        var ckvalue = $("input[name='change_type']:checked").val();
        var data = "";
        if (ckvalue == 'byuser') {
            var chooseobj = $("#chooseuser>option:selected");
            if (chooseobj.val() == "") {
                alert("未选中人员！");
                return;
            } else {
                data = "type=byuser&stepid=" + chooseobj.attr("stepid") + "&wfid=" + chooseobj.attr("wfid") + "&user=" + chooseobj.attr("user") + "&nodename=" + chooseobj.attr("nodename");
            }
        } else if (ckvalue == 'bystep') {
            var chooseobj = $("#choosestep>option:selected");
            if (chooseobj.val() == "") {
                alert("未选中环节！");
                return;
            } else {
                data = "type=bystep&stepid=" + chooseobj.attr("stepid") + "&wfid=" + chooseobj.attr("wfid") + "&nodename=" + chooseobj.attr("nodename");
            }
        } else if (ckvalue == 'byorg') {
            var chooseobj = $("#choosedept>option:selected");
            if (chooseobj.val() == "") {
                alert("未选中部门！");
                return;
            } else {
                data = "type=byorg&stepid=1001&wfid=" + chooseobj.attr("wfid") + "&user=" + chooseobj.attr("user") + "&deptid=" + chooseobj.attr("deptid") + "&p_stepid=" + chooseobj.attr("p_stepid") + "&p_wfid=" + chooseobj.attr("p_wfid");
            }
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseurl + "index.php/config/route/workflowroute",
            data: "etuid=" + id + "&" + data,
            success: function (result) {
                if (result.status == 'success') {
                    layer.alert("操作成功");
                    //刷新表格
                    lookup();
                } else {
                	layer.alert("系统错误：" + result.msg);
                }
            }
        });
    }
</script>

