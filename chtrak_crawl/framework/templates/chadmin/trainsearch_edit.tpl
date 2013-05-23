<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="/public/css/bootstrap.min.css" />
<link rel="stylesheet" href="/public/css/bootstrap-responsive.min.css" />
<script type="text/javascript" src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
<title>站站之间的车次编辑</title>
</head>

<body>
<!--左右结构-->
<div class="container-fluid" style=" padding-top:20px;">
	<div class="row-fluid">
		{include file="chadmin/left.tpl"}
		<div class="span10">
			<div class="well" style="background:none;">
				<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
					<form class="form-horizontal" id="from1" name="form1" method="post" action="/chadmin/trainsearch.php?do=save">
					<table align="center" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th colspan="6">站站之间的车次编辑</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>车次</td>
								<td>
									<input type="text" class="span2" name="trainnumber" value="{$train_info.Trainnumber}" readonly="readonly" >
								</td>
								<td>车站顺序</td>
								<td colspan="3"><input type="text" class="span2" name="sequence" value="{$train_info.Sequence}" readonly="readonly"></td>
							</tr>
							<tr>
								<td>出发省份名</td>
								<td><input type="text" class="span2" name="fromprovince" value="{$train_info.FromProvince}" readonly="readonly" ></td>
								<td>英文出发省份名</td>
								<td colspan="3"><input type="text" class="span2" name="enfromprovince" value="{$train_info.EnFromProvince}" readonly="readonly" ></td>
							</tr>
							<tr>
								<td>出发城市名</td>
								<td><input type="text" class="span2" name="fromcity" value="{$train_info.FromCity}" readonly="readonly" ></td>
								<td>英文出发城市名</td>
								<td colspan="3"><input type="text" class="span2" name="enfromcity" value="{$train_info.EnFromCity}" readonly="readonly" ></td>
							</tr>
							<tr>
								<td>出发站名</td>
								<td><input type="text" class="span2" name="from" value="{$train_info.From}" readonly="readonly" ></td>
								<td>英文出发站名</td>
								<td><input type="text" class="span2" name="enfrom" value="{$train_info.EnFrom}" readonly="readonly" ></td>
								<td>出发站权重</td>
								<td colspan="3"><input type="text" class="span1" name="fromweight" value="{$train_info.FromWeight}" readonly="readonly" ></td>
							</tr>
							<tr>
								<td>到达省份名</td>
								<td><input type="text" class="span2" name="toprovince" value="{$train_info.ToProvince}" readonly="readonly" ></td>
								<td>英文到达省份名</td>
								<td colspan="3"><input type="text" class="span2" name="entoprovince" value="{$train_info.EnToProvince}" readonly="readonly" ></td>
							</tr>
							<tr>
								<td>到达城市名</td>
								<td><input type="text" class="span2" name="tocity" value="{$train_info.ToCity}" readonly="readonly" ></td>
								<td>英文到达城市名</td>
								<td colspan="3"><input type="text" class="span2" name="entocity" value="{$train_info.EnToCity}" readonly="readonly" ></td>
							</tr>
							<tr>
								<td>到达站名</td>
								<td><input type="text" class="span2" name="to" value="{$train_info.To}" readonly="readonly" ></td>
								<td>英文到达站名</td>
								<td><input type="text" class="span2" name="ento" value="{$train_info.EnTo}" readonly="readonly" ></td>
								<td>到达站权重</td>
								<td><input type="text" class="span1" name="toweight" value="{$train_info.ToWeight}" readonly="readonly" ></td>
							</tr>
							<tr>
								<td>出发时间</td>
								<td><input type="text" class="span2" name="deptime" value="{$train_info.Deptime}" ></td>
								<td>到达时间</td>
								<td colspan="3">
									<input type="text" class="span2" name="arrtime" value="{$train_info.Arrtime}" >&nbsp;&nbsp;
									注意时间格式， 02:30:00
								</td>
							</tr>
							<tr>
								<td>运行时间</td>
								<td><input type="text" class="span1" name="traveltime" value="{$train_info.Traveltime}" >分钟</td>
								<td>距离</td>
								<td colspan="3"><input type="text" class="span1" name="distancekm" value="{$train_info.Distancekm}" >千米</td>
							</tr>
							<tr>
								<td>硬座</td>
								<td><input type="text" class="span1" name="hardseat" value="{$train_info.Hardseat}" >元</td>
								<td>软座</td>
								<td colspan="3"><input type="text" class="span1" name="softseat" value="{$train_info.Softseat}" >元</td>
							</tr>
							<tr>
								<td>硬卧</td>
								<td><input type="text" class="span1" name="hardsleeper" value="{$train_info.Hardsleeper}" >元</td>
								<td>软卧</td>
								<td colspan="3"><input type="text" class="span1" name="softsleeper" value="{$train_info.Softsleeper}" >元</td>
							</tr>
							<tr>
								<td>豪华软座</td>
								<td><input type="text" class="span1" name="deluxesleeper" value="{$train_info.Deluxesleeper}" >元</td>
								<td>商务座</td>
								<td colspan="3"><input type="text" class="span1" name="businessclassseat" value="{$train_info.Businessclassseat}" >元</td>
							</tr>
							<tr>
								<td>一等座</td>
								<td><input type="text" class="span1" name="firstclassseat" value="{$train_info.Firstclassseat}" >元</td>
								<td>二等座</td>
									<td><input type="text" class="span1" name="secondclasseat" value="{$train_info.Secondclasseat}" >元</td>
								<td>特等座</td>
								<td><input type="text" class="span1" name="topclassseat" value="{$train_info.Topclassseat}" >元</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td colspan="5">
									<input type="hidden" name="trainid" value="{$train_info.ID}">
									<input type="submit" class="btn btn-primary" name="submit1" value=" 保  存 ">
									&nbsp;&nbsp;<a href="/chadmin/trainsearch.php?do=manage">返回管理页</a>
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
