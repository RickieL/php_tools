<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<link rel="stylesheet" href="/public/css/bootstrap.min.css" />
<link rel="stylesheet" href="/public/css/bootstrap-responsive.min.css" />
<script type="text/javascript" src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
<title>登录</title>
</head>

<body>
<div class="container-fluid" style=" padding-top:20px;">
	<div class="row-fluid">
		<div class="span12">
		<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
			<center>
            	{if $lonin_err}
                <div class="alert alert-error">
                {$lonin_err}
                </div>
            	{/if}
                <form class="well form-inline" action="/index.php?do=login" method="post">
                  <input type="text" class="input-middle"  id="username" name="username" placeholder="用户名">
                  <input type="password" class="input-middle"  id="pass" name="pass" placeholder="密码">
                  <button type="submit" class="btn btn-large btn-primary">登录</button>
                </form>
			</center>
		</div>
	</div>
</div>
</body>
</html>