<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<link rel="stylesheet" href="/public/css/bootstrap.min.css" />
<link rel="stylesheet" href="/public/css/bootstrap-responsive.min.css" />
<script type="text/javascript" src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
<title>城市和站点管理</title>
</head>

<body>
<div class="container-fluid" style=" padding-top:20px;">
	<div class="row-fluid">
		{include file="chadmin/left.tpl"}
		<div class="span10">
        	<h2>城市站点管理</h2><br />
             <form class="form-inline" action="" method="post">
                  <input type="text" class="input-middle"  id="searchkey" name="searchkey" placeholder="城市名/站点名" {if $searchkey} value="{$searchkey}" {/if}>
                  <button type="submit" class="btn btn-primary">搜索站点/城市</button>
             </form>
             <hr />
			<table class="table table-striped">
				<thead>
					<th>删除</th>
					<th>EnProvinceName</th>
					<th>ProvinceName</th>
					<th>EnCityName</th>
					<th>CityName</th>
					<th>EnStationName</th>
					<th>StationName</th>
					<th>Weight</th>
					<th>选项</th>
				</thead>
				<tr id="stationId">
					<td><button type="button" class="btn btn-primary"  id="AddStation" name="AddStation"  onclick="AddStation()">添加</button></td>
					<td><input type="text"  class="input-small" id="EnProvinceName" name="EnProvinceName" placeholder="EN省份名" value="" /></td>
					<td><input type="text"  class="input-small" id="ProvinceName" name="ProvinceName" placeholder="省份名" value="" style="width:50px;" /></td>
					<td><input type="text"  class="input-small" id="EnCityName" name="EnCityName"  placeholder="EN城市名" value="" /></td>
					<td><input type="text"  class="input-small" id="CityName" name="CityName"  placeholder="城市名" value="" /></td>
					<td><input type="text"  class="input-medium" id="EnStationName" name="EnStationName"  placeholder="EN站名"  value="" /></td>
					<td><input type="text"  class="input-small" id="StationName" name="StationName"  placeholder="站名" value="" /></td>
					<td><input type="text"  class="input-small" id="Weight" name="Weight" value="" style="width:30px;" /></td>
					<td><button type="button" class="btn btn-danger"  id="AddStation" name="AddStation"  onclick="AddStation()">添加站点</button></td>
				</tr>
				{foreach $station_arr as $item}
				<tr id="stationId_{$item.ID}">
					<td>
					<button type="button" class="btn btn-danger"  id="DeleteStation_{$item.ID}" name="DeleteStation_{$item.ID}"  onclick="DeleteStation({$item.ID})">删除</button>
					</td>
					<td><input type="text"  class="input-small" id="EnProvinceName_{$item.ID}" name="EnProvinceName_{$item.ID}" value="{$item.EnProvinceName}" /></td>
					<td><input type="text"  class="input-small" id="ProvinceName_{$item.ID}" name="ProvinceName_{$item.ID}" value="{$item.ProvinceName}" style="width:50px;" /></td>
					<td><input type="text"  class="input-small" id="EnCityName_{$item.ID}" name="EnCityName_{$item.ID}" value="{$item.EnCityName}" /></td>
					<td><input type="text"  class="input-small" id="CityName_{$item.ID}" name="CityName_{$item.ID}" value="{$item.CityName}" /></td>
					<td><input type="text"  class="input-medium" id="EnStationName_{$item.ID}" name="EnStationName_{$item.ID}" value="{$item.EnStationName}" /></td>
					<td><input type="text"  class="input-small" id="StationName_{$item.ID}" name="StationName_{$item.ID}" value="{$item.StationName}" /></td>
					<td><input type="text"  class="input-small" id="Weight_{$item.ID}" name="Weight_{$item.ID}" value="{$item.Weight}" style="width:30px;" /></td>
					<td><button type="button" class="btn btn-primary" id="SaveChangeStation_{$item.ID}" name="SaveChangeStation_{$item.ID}"   onclick="SaveChangeStation({$item.ID})">保存修改</button></td>
				</tr>
				{/foreach}
			</table>
			<div class="pagination pagination-right">{$page_html}</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="/public/js/managestation.js"></script>
</body>
</html>