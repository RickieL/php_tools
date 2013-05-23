<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="/public/css/bootstrap.min.css" />
<link rel="stylesheet" href="/public/css/bootstrap-responsive.min.css" />
<script type="text/javascript" src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
<title>提示信息</title>
</head>

<body>
<!--左右结构-->
<div class="container-fluid" style=" padding-top:20px;">
	<div class="row-fluid">
		{include file="chadmin/left.tpl"}
		<div class="span10">
			<div class="well" style="background:none;">
				<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
					    <div class="alert alert-success">
							<h3><font {if $flag == 2} color="#FF0000"{/if}><h2 class="alert-heading">提示信息</h2>
							{$info}</font><br><br><a href="{$href}">点击这里跳转</a></h3>
						</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
