//获取跳转地址
function get_formerurl(){
    return $('#former_url').val();
}


//转换内容为表格
function convert_message_to_table(msg) {
    if (msg == null || msg == undefined || msg == '') {
        return '';
    }
   
    if (msg.indexOf('#th#') == -1 && msg.indexOf('#td#') == -1) {
        
        msg = "&nbsp;"+"#th#"+msg+"#td#";
    }

    //处理表格
    var msg_array = msg.split('#td#');
    var html = '<table style="border:none;border-collapse:collapse;margin:auto;">';
    for (var index = 0; index < msg_array.length; index++) {
        // 每条拆分
        var each_array = msg_array[index].split('#th#');
        if (each_array[0] == '') {
            continue;
        }
        html += '<tr><td style="text-align:right;padding-right:3px;white-space:nowrap;">' + each_array[0] + '</td><td style="text-align:left;padding-left:3px;white-space:nowrap;">' + each_array[1] + '</td></tr>';
    }
    html += '</table>';
    
    return html;
}


//弹出框 for validate
function show_message(message) {

    message = convert_message_to_table(message);
    
    //计算表格高度(行高*25+上下间距20+确定按钮高度80)
    var height_px=$(message).find("tr").length*25+40;

    var dialogHTML='<div style="width:400px;height:'+height_px+'px;">'
        + '			    <div style="position:absolute;left:10px;top:10px;"><img src="common/images/alert_mini.png"/></div>'
        +'              <div style="width:100%;margin-top:10px;">'
        + message
        +'              </div>'
        + '</div>';
    var errorinfo = layer.open({
    	type: 1,   //0-4的选择,
        title: '提示信息',
        border: [0],
        content: dialogHTML,
        area:['auto','auto'],
        btns: 1,
        btn: ['确定'],
        yes: function () {
            layer.close(errorinfo);
        }
    });
}

//送审处理
function __doAction(id) {
    //驳回时需要校验意见不能为空。
    //2013-8-28 增加循环会签驳回、会签驳回，驳回意见均不能为空
    var comm = $('#__comm').val();
    if (comm == "" && (id == -100 || id == -200 || id == -250)) {
        layer.alert("驳回必须填写审批意见", 8);
        $('#__comm').focus();
        return;
    }
    $('#__actionid').val(id);
    $('#__flowcomm').val($('#__comm').val());

    //提交的时候处理
    var loading = layer.load('正在处理中...');

    $('#theForm').ajaxSubmit({
        dataType: 'json',
        error: function (request, status, error) {
            layer.close(loading);
            layer.alert("系统出错，请联系管理员！");
        },
        success: function (json) {
            if (json.status == "error" ) {
                show_message(json.message);
                layer.close(loading);
            } else {
                //通过
                var html = '';

                $(json.nextuser).each(function () {
                    if (html != '') {
                        html += ',';
                    }
                    html += this.u_name;
                });

                if (html != '' && json.flowstatus != 'Reject') {
                    html = '您的单据已送审至' + html + '审批!';
                } else if(html != '' && json.flowstatus == 'Reject'){
                    html = '您的单据已驳回至' + html + '处理!';
                }else{
                    html = json.message;
                }
                if (id == -99) html = "单据已收回，请在'待处理文档'中查看";

                if (typeof(json.flowstatus) != 'undefined' && json.flowstatus == 'finished') {
                    html = "审批完成";
                }
                var dialogHTML = '<div style="width:350px;height:250px;">';
                dialogHTML += '<div style="font-size:16px;font-weight:bold;padding:60px;"><img src="common/images/check_mini.png"/><span id="text_nextto" style="padding-left:5px;">' + html + '</span></div>';
                dialogHTML += '<div style="padding-left:120px;"><span id="dialog_sec">3</span>秒后自动跳转</div>';
                dialogHTML +='</div>';
                var pageinfo = layer.open({
                    type: 1,   //0-4的选择,
                    title: '提示信息',
                    border: [0],
                    area: ['auto', 'auto'],
                    content: dialogHTML,
                    btns: 1,
                    btn: ['关闭'],
                    yes: function () {
                        layer.close(pageinfo);
                    },
                    //关闭时跳转
                    end: function(){
                        window.location=get_formerurl();
                    }
                });
                var intervalid="";
                intervalid=setInterval(
                  function(){
                      var i = $("#dialog_sec").html();
                      if(i=='') i=0;
                      i=parseFloat(i);

                      i--;

                      if (i == 0) {
                          layer.close(pageinfo);
                          clearInterval(intervalid);
                      }else{
                          $("#dialog_sec").html(i);
                      }
                  },1000);
            }
        }
    });

}

/**
 * 格式化数字三位一撇
 * money :被格式化的数字
 * decimals:是否保留小数 1保留，0不保留
 */
function FormatNumber(money,decimals)
{
    if(decimals==undefined){
        decimals = 1;
    }
    //判断是否为数字(支持：负数：-.1 -0.1 -9.9 -9 整数：.1 0.1 9.8 10)
    reg=/^-?\d*(\.\d+)?$/;
    //非数字跳出
    if(!reg.test(money)) return money;
    //检查是否为小数格式化（格式化类型 含小数：四舍五入×××.××）
    money=String(parseFloat(money).toFixed(2));
    //将小数点替换
    money=money.replace(".",",");
    //3位一撇处理
    var re=/(-?\d)(\d{3},)/;  
    while(re.test(money)){  
        money=money.replace(re,"$1,$2");  
    }
    //处理小数小数点
    money=money.replace(/,(\d\d)$/,".$1");   
    if(decimals==0){//是否保留小数
        money = money.substring(0, money.length-3);
    }
    return money; 
}
