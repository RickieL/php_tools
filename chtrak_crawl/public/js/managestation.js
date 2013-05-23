
//保存站点信息
function SaveChangeStation(id)
{
	
	var EnProvinceName = $("input[name='EnProvinceName_"+id+"']").val();
	var ProvinceName = $("input[name='ProvinceName_"+id+"']").val();
	var EnCityName = $("input[name='EnCityName_"+id+"']").val();
	var CityName = $("input[name='CityName_"+id+"']").val();
	var EnStationName = $("input[name='EnStationName_"+id+"']").val();
	var StationName = $("input[name='StationName_"+id+"']").val();
	var Weight = $("input[name='Weight_"+id+"']").val();
	
    var ID = id;
    
	$.post("/chadmin/managestation.php?do=savestation", {'EnProvinceName':EnProvinceName,'ProvinceName':ProvinceName,'EnCityName':EnCityName,'CityName':CityName,'EnStationName':EnStationName,'StationName':StationName,'Weight':Weight,'ID':ID} , function(data){
		if(data.code == 0)
		{
			alert(data.msg);
			location.reload();
		}
		else
		{
			alert(data.msg);
			location.reload();
		}
	}, "json");
	
}

//删除站点
function DeleteStation(id)
{
	if(window.confirm('删除站点，将同时删除含有该站点的乘车信息\n\n确认删除？'))
	{
		$.post("/chadmin/managestation.php?do=delstation", {'ID':id} , function(data){
			if(data.code == 1){
				alert(data.msg);
				$("#stationId_"+id).remove();
			}else{
				alert(data.msg);	
			}
		}, "json");
	}
}

//添加站点
function AddStation()
{
	var EnProvinceName = $("input[name='EnProvinceName']").val();
	var ProvinceName = $("input[name='ProvinceName']").val();
	var EnCityName = $("input[name='EnCityName']").val();
	var CityName = $("input[name='CityName']").val();
	var EnStationName = $("input[name='EnStationName']").val();
	var StationName = $("input[name='StationName']").val();
	var Weight = $("input[name='Weight']").val();
	

	if (StationName.length == '')
	{
		alert('必须输入站点名！');
		return false;
	}
	
	if (Weight.length != 0 && !parseInt(Weight))
	{
		alert("权重: "+Weight+" 不合乎数据要求，要求必须为数字！");
		return false;
	}
	
	$.post("/chadmin/managestation.php?do=addstation", {'EnProvinceName':EnProvinceName, 'ProvinceName':ProvinceName, 'EnCityName':EnCityName, 'CityName':CityName, 'EnStationName':EnStationName, 'StationName':StationName, 'Weight':Weight} , function(data){
		if (data.code == 1){
			alert(data.msg);
			location.reload();
		}else{
			alert(data.msg);
		}
	}, "json");
	
	
}
