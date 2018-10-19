/*
	防止重复提交_zhouxiang_2014-9-2
*/
(function(){	
	// token 信息
	var info = {
		key : null,
		value : null
	};
	
	// 命名空间
	$.token = {
		// 初始化
		set : function(token){
			tokens = token.split('_');
			info.key = tokens[0];
			info.value = tokens[1];
			$('._key').text(info.key);
			$('._value').text(info.value);
		},
		// 提交
		submit : function(data){
			if(typeof(data) == 'string'){ // 字符串形式
				data += '&token_key='+info.key+'&token_value='+info.value;
			}else if(data instanceof Array){ // 数组形式
				data.push( { name:'token_key', value:info.key } );
				data.push( { name:'token_value', value:info.value } );
			}else{ // 对象形式
				data.token_key = info.key;
				data.token_value = info.value;
			}
			return data;
		},
		// 测试用
		show : function(session){
			var html = '<table>';
			for(var key in session){
				var value = session[key];
				html += '<tr><td>'+ key + '</td><td>-</td><td>' + value + '</td></tr>';
			}
			html += '</table>';
			$('#session').html(html);
		}
	};
})();
	