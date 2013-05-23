<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="/public/css/bootstrap.min.css" />
<link rel="stylesheet" href="/public/css/bootstrap-responsive.min.css" />
<script type="text/javascript" src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/public/js/bootstrap-modal.js"></script>
<title>站站之间的车次管理</title>
</head>

<body>
<!--左右结构-->
<div class="container-fluid" style=" padding-top:20px;">
	<div class="row-fluid">
		{include file="chadmin/left.tpl"}
		<div class="span10">
			<div class="well" style="background:none;">
				<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
					<form class="form-horizontal" id="from1" name="form1" method="post" action="/chadmin/trainsearch.php?do=manage">
					<table align="center" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th colspan="5">站站之间的车次管理</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>类型:</td>
								<td>
									<input type="radio" name="traintype" value="1" {if $traintype == 1} checked="checked"{/if}>CDG&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="traintype" value="2" {if $traintype == 2} checked="checked"{/if}>其他
								</td>
								<td >车次:</td>
								<td>
									<input type="text" name="start_number" value="{$start_number}" class="span2" >
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td>出发站:</td>
								<td>
									<input type="text" name="start_station" value="{$start_station}" class="span2" >
								</td>
								<td>到达站:</td>
								<td>
									<input type="text" name="end_station" value="{$end_station}" class="span2" >
								</td>
								<td>
									<input type="submit" value=" 搜 索 " class="btn">
								</td>
							</tr>
						</tbody>
					</table>
					</form>
					
					<table align="center" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="5%">售票</th>
								<th width="10%">车次</th>
								<th width="20%">出发站信息</th>
								<th width="20%">到达站信息</th>
								<th width="10%">时间</th>
								<th width="5%">距离</th>
								<th width="20%">票价</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							{foreach $train_data as $key => $val}
							<tr id="train_{$val.ID}">
								<td style="line-height:15px;">
									<input type="checkbox" value="1" id="{$val.ID}" class="train_check" {if $val.OnSale == 1}checked="checked"{/if}>
								</td>
								<td style="line-height:15px;">{if $val.Trainnumber}{$val.Trainnumber}{else}无车次信息{/if}</td>
								<td style="line-height:15px;">
									省:{$val.FromProvince}{if $val.EnFromProvince}({$val.EnFromProvince}){/if}<br>
									市:{$val.FromCity}{if $val.EnFromCity}({$val.EnFromCity}){/if}<br>
									站:{$val.From}{if $val.EnFrom}({$val.EnFrom}){/if}
								</td>
								<td style="line-height:15px;">
									省:{$val.ToProvince}{if $val.EnToProvince}({$val.EnToProvince}){/if}<br>
									市:{$val.ToCity}{if $val.EnToCity}({$val.EnToCity}){/if}<br>
									站:{$val.To}{if $val.To}({$val.EnTo}){/if}
								</td>
								<td style="line-height:15px;">
									出发:{$val.Deptime}<br/>
									到达:{$val.Arrtime}<br/>
									行车:{if $val.Traveltime}{$val.Traveltime}分钟{/if}
								</td>
								<td style="line-height:15px;">{if $val.Distancekm}{$val.Distancekm}km{/if}</td>
								<td style="line-height:15px;">
									硬座:{$val.Hardseat}&nbsp;&nbsp;软座:{$val.Softseat}<br/>
									硬卧:{$val.Hardsleeper}&nbsp;&nbsp;软卧:{$val.Softsleeper}<br/>
									豪华软卧:{$val.Deluxesleeper}&nbsp;&nbsp;商务座:{$val.Businessclassseat}<br/>
									一等座:{$val.Firstclassseat}&nbsp;&nbsp;二等座:{$val.Secondclasseat}&nbsp;&nbsp;特等座:{$val.Topclassseat}
								</td>
								<td style="line-height:15px;">
									<a href="/chadmin/trainsearch.php?do=edit&id={$val.ID}">编辑</a>
									<a href="javascript://" onClick='show_confirm("{$val.ID}");return false;'>删除</a>
								</td>
							</tr>
							{/foreach}
							<tr>
								<td colspan="8">
									<input type="checkbox" value="1" checked="checked" class="all_check" >&nbsp;&nbsp;
									<input type="button" value="保存设置" id="saveset" class="btn btn-primary">
 								</td>
							</tr>
						</tbody>
					</table>
					<div class="pagination pagination-right">{$page_html}</div>
				</div>
			</div>
		</div>
	</div>
</div>
{literal}
<script type="text/javascript">
function show_confirm(id){
	var r=confirm("确认删除该条数据么？");
	if (r==true){
  		$.post("/chadmin/trainsearch.php?do=del",{id : id},function(data){
			if(data.code == 1){
				alert(data.info);
				$("#train_"+id).remove();
			}
			else if(data.code == 2){
				alert(data.info);
			}
		},'json');
  	}
}

$(".train_check").each(function(){
	if(this.checked == false){
		$('.all_check').attr('checked', false);
	}
}); 

$('.all_check').click(function(){
	if(this.checked == true){
		$(".train_check").each(function(){this.checked = true;});
	}else{
		$(".train_check").each(function(){this.checked = false;});
	} 
});

$(".train_check").click(function(){
	var flag = true;
	$(".train_check").each(function(){
		if(this.checked == false){
			flag = false;
		}
	});
	
	$('.all_check').attr('checked', flag);
});

$('#saveset').click(function(){
	var ids = set = '';
	$('.train_check').each(function(){
		var id = parseInt($(this).attr('id'));
		if(ids){
			ids = ids + "," + id;
		}else{
			ids = id;
		}
		if(this.checked == true){
			if(set){
				set = set + ",1";
			}else{
				set = "1";
			}
		}else{
			if(set){
				set = set + ",0";
			}else{
				set = "0";
			}
		}
	});
	
	if(ids && set){
		$.post("/chadmin/trainsearch.php?do=saveset",{ids : ids, set : set},function(data){
			if(data.code){
				alert(data.info);
			}
		},'json');
	}
});
</script>
{/literal}
</body>
</html>
