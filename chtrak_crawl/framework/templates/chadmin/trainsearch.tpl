<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="/public/css/bootstrap.min.css" />
<link rel="stylesheet" href="/public/css/bootstrap-responsive.min.css" />
<script type="text/javascript" src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
<title>站站之间的车次采集</title>
</head>

<body>
<!--左右结构-->
<div class="container-fluid" style=" padding-top:20px;">
	<div class="row-fluid">
		{include file="chadmin/left.tpl"}
		<div class="span10">
			<div class="well" style="background:none;">
				<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
					<form class="form-horizontal" id="from1" name="form1" method="post" action="/chadmin/trainsearch.php?do=search">
					<table align="center" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th colspan="2">站站之间的车次采集</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="10%">出发城市/站:</td>
								<td>
									<input type="text" name="start" value="" class="span2" >
								</td>
							</tr>
							<tr>
								<td>到达城市/站:</td>
								<td>
									<input type="text" name="end" value="" class="span2" >
								</td>
							</tr>
							<tr>
								<td>反向采集:</td>
								<td>
									<label class="checkbox" style="width:60px;"><input type="checkbox" checked name="round" value="1"> 反向采集</label>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="submit" value="确定采集" class="btn">
								</td>
							</tr>
						</tbody>
					</table>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
