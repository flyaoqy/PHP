<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="pragma" content="no-cache"> 
	<meta http-equiv="cache-control" content="no-cache"> 
	<meta http-equiv="expires" content="0">  
    <base href="<?php echo base_url(); ?>" />

</head>
<body>
	<form action="index.php/config/workflow/wfconfig/save" method="post">
		<input type="hidden" name="wfuid" value="<?php echo $row['wf_uid']?>"/>
		<h3><?php echo $row['wf_name']?></h3>
		<input  class="btn" type="button" value="保存" onclick="document.forms[0].submit()"/>
		<textarea style="height:100%;width:100%" name="filename"><?php echo $row['wf_filename']?></textarea>	
	
	</form>
</body>
</html>
