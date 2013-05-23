<?php
/**
 *  管理省份、城市、站点
 */

require_once ('../common.inc.php');

class ManageStation extends Action
{
	//站点管理页
	public function doDefault()
	{
		global $app;
		if ($_POST['searchkey'])
			$searchkey = mysql_real_escape_string(trim($_POST['searchkey']));
		else 
			$searchkey = '';
		
		$page = $app->page();
		$page->value('cur_left', 'managestation');
		
		//获取 站点 数据条数
		importModule("Chadmin.ManageStationInfo");
		$station_obj = new ManageStationInfo();
		$total = $station_obj->getStationTotal($searchkey);
		
		
		if ($total > 0)
		{
			//翻页信息
			$pagesize = 25;
			$total_page = ceil($total/$pagesize);
			$total_page = $total_page < 1 ? 1 : $total_page;
			$p = 1;
			if (isset($_GET['p']))
			{
				$ptmp = intval($_GET['p']);
				$p = ($ptmp > 1) ? $ptmp : 1;
			}
			$p = $p < 1 ? 1 : $p;
			$p = $p > $total_page ? $total_page : $p;
			$page_html = $page->getPageStr($total_page, $p);
			//分页
			$page->value('page_html', $page_html);
			
			//开始位置
			$start = ($p>1) ? ($p-1)*$pagesize : 0;
			
			//获取站点数据
			$station_arr =  $station_obj->getStationPage($start,$pagesize,$searchkey);
			
			$page->value('station_arr', $station_arr);
			$page->value('left_flag', 'station');
			$page->value('searchkey', $searchkey);
			
		}
		$page->output();
	}
	
	/**
	 *  更新站点信息
	 */
	public function doSaveStation()
	{
		$EnProvinceName = trim($_POST['EnProvinceName']);
		$ProvinceName = trim($_POST['ProvinceName']);
		$EnCityName = trim($_POST['EnCityName']);
		$CityName = trim($_POST['CityName']);
		$EnStationName = trim($_POST['EnStationName']);
		$StationName = trim($_POST['StationName']);
		$Weight = intval(trim($_POST['Weight']));
		$ID = intval(trim($_POST['ID']));

		$NewStation = array('EnProvinceName'=>$EnProvinceName,
						'ProvinceName'=>$ProvinceName,
						'EnCityName'=>$EnCityName,
						'CityName'=>$CityName,
						'EnStationName'=>$EnStationName,
						'StationName'=>$StationName,
						'Weight'=>$Weight,
						'ID'=>$ID
				);
		
		//查询原站点信息
		importModule("Chadmin.ManageStationInfo");
		$station_obj = new ManageStationInfo();
		$OrgStation = $station_obj->getStationByID($ID);

		if (!$OrgStation)
		{
			$d_arr = array('code'=>'0', 'msg'=>"更新失败！\n该ID非法。");
		}
		else 
		{
			//更新站点信息
			if ($EnProvinceName != $OrgStation['EnProvinceName'] || $ProvinceName != $OrgStation['ProvinceName'] || $EnCityName != $OrgStation['EnCityName'] || $CityName != $OrgStation['CityName'] || $EnStationName != $OrgStation['EnStationName'] || $StationName != $OrgStation['StationName'] || $Weight != $OrgStation['Weight'])
			{
				if ($StationName != $OrgStation['StationName'])
				{
					if ($station_obj->getStationByName($StationName))
					{
						$d_arr = array('code'=>'0', 'msg'=>"更新失败！\n已存在该站点名");
					}
					else 
					{
						$station_obj->updateStationInfo($NewStation, $OrgStation);
						$d_arr = array('code'=>'1', 'msg'=>"更新成功");
					}
				}
				else 
				{
					$station_obj->updateStationInfo($NewStation, $OrgStation);
					$d_arr = array('code'=>'1', 'msg'=>"更新成功");
				}
			}
		}
		
		$e = json_encode($d_arr);
		echo $e;
	}
	
	/**
	 *  删除站点信息
	 */
	public function doDelStation()
	{
		$ID = intval(trim($_POST['ID']));
	
		//查询原站点信息
		importModule("Chadmin.ManageStationInfo");
		$station_obj = new ManageStationInfo();
		$OrgStation = $station_obj->getStationByID($ID);

		if (!$OrgStation)
		{
			$d_arr = array('code'=>'0', 'msg'=>"删除失败\n该ID非法。");
		}
		elseif ($OrgStation['StationName'] == '')
		{
			$d_arr = array('code'=>'0', 'msg'=>"删除失败\n车站名不能为空值！");
		}
		else
		{
			//删除站点 by ID
			$station_obj->delStation($OrgStation);
			$d_arr = array('code'=>'1', 'msg'=>"删除成功");
		}
		$e = json_encode($d_arr);
		echo $e;
	}
	
	/**
	 *  手动增加新站点
	 */
	public function doAddStation()
	{
		
	
		$station_info = array();
		
		$station_info['EnProvinceName'] = trim($_POST['EnProvinceName']);
		$station_info['ProvinceName'] = trim($_POST['ProvinceName']);
		$station_info['EnCityName'] = trim($_POST['EnCityName']);
		$station_info['CityName'] = trim($_POST['CityName']);
		$station_info['EnStationName'] = trim($_POST['EnStationName']);
		$station_info['StationName'] = trim($_POST['StationName']);
		$station_info['Weight'] = intval(trim($_POST['Weight']));
		
		//查询原站点信息
		importModule("Chadmin.ManageStationInfo");
		$station_obj = new ManageStationInfo();
		$is_have = $station_obj->getStationByName($station_info['StationName']);
	
		if ($is_have)
		{
			$d_arr = array('code'=>'0', 'msg'=>"该站点已经存在，中文站点名不能重复！");
		}
		else
		{
			$this->db->clear();
			$this->db->addTable("station");
			$this->db->insert($station_info);
			
			$d_arr = array('code'=>'1', 'msg'=>"添加成功");
		}
		$e = json_encode($d_arr);
		echo $e;
	}
}

$app->run ();