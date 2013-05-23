<?php
/*
 *  城市站点管理
 * @author RickieL
 */
class ManageStationInfo
{
	private $db;

	public function __construct()
	{
		global $app;
		$this->db = $app->orm()->query();
	}

	
	/**
	 * 查询 城市/站点 数据条数
	 * $search_cs  string  城市或者站点中文名
	 */
	public function getStationTotal($search_cs = '')
	{
		$sql = "SELECT COUNT(ID) FROM station  WHERE 1=1";
		if ($search_cs)
		{
			$sql .= " AND (CityName = '".$search_cs."' OR StationName LIKE '".$search_cs."%')";
		}
		$total = $this->db->clear()->getValue($sql);
	
		return $total;
	}
	
	
	/**
	 * 按ID查询城市/站点信息
	 */
	public function getStationByID($ID)
	{
		$sql = "SELECT  ID,EnProvinceName,ProvinceName,EnCityName,CityName,EnStationName,StationName,Weight FROM station where ID=" . $ID;
		$OrgStation = $this->db->clear()->getRow($sql);
		return $OrgStation;
	}
	
	/**
	 * 根据站点名称查询城市站点信息
	 * @param string $StationName 站点名称 
	 */
	public function getStationByName($StationName, $type='')
	{
		$StationName = trim($StationName);
		if(!$StationName)
		{
			return false;
		}
		
		$this->db->clear();
		$this->db->addTable('station');
		$this->db->addWhere('StationName', $StationName);
		$result = $this->db->getRow();
		if ($result)
		{
			return $result;
		}
		else
		{
			if ($type == 'insert')
			{
				$station_info = array();
				$station_info['EnProvinceName'] = $station_info['ProvinceName'] = $station_info['EnCityName'] = $station_info['CityName'] = $station_info['EnStationName'] = $station_info['Weight'] = '';
				$station_info['StationName'] = $StationName;
				
				$this->db->clear();
				$this->db->addTable("station");
				$this->db->insert($station_info);
				
				$ID = 0;
				$ID = $this->db->getLastId();
				if ($ID)
				{
					$station_info['ID'] = $ID;
					return $station_info;
				}
				else
				{
					return false;
				}
			}
			else 
			{
				return false;
			}
		}
	}

	/**
	 * 查询 城市/站点 分页数据
	 * $start int 查询的起始位置
	 * $limit int 查询的条数限制
	 * $search_cs  string  城市或者站点中文名
	 */
	public function getStationPage($start, $limit, $search_cs = '')
	{
		$sql = "SELECT * FROM station WHERE 1=1";
		if ($search_cs)
		{
			$sql .= " AND (CityName = '".$search_cs."' OR StationName LIKE '".$search_cs."%')";
		}
		$sql .= " ORDER BY ID DESC LIMIT " . $start . "," . $limit;
		$result = $this->db->clear()->getArray($sql);

		return $result;
	}
	
	/**
	 * 更新 城市/站点 
	 * $newstation_arr array 填写的新的站点信息
	 * $orgstation_arr array 原始的站点信息
	 */
	public function updateStationInfo($newstation_arr, $orgstation_arr)
	{
		//更新station表
		$this->db->clear();
		$this->db->addTable('station');
		$this->db->addValue('EnProvinceName',$newstation_arr['EnProvinceName']);
		$this->db->addValue('ProvinceName',$newstation_arr['ProvinceName']);
		$this->db->addValue('EnCityName',$newstation_arr['EnCityName']);
		$this->db->addValue('CityName',$newstation_arr['CityName']);
		$this->db->addValue('EnStationName',$newstation_arr['EnStationName']);
		$this->db->addValue('StationName',$newstation_arr['StationName']);
		$this->db->addValue('Weight',$newstation_arr['Weight']);
		$this->db->addWhere('ID', $newstation_arr['ID'], _ORM_OP_EQ);
		$this->db->update();
				
		//更新schedule表 出发地和到达地 为station的
		$station_from = array('start'=>$orgstation_arr['StationName']);
		$station_to = array('end'=>$orgstation_arr['StationName']);
		importModule("Chadmin.TrainSearchInfo");
		$train_obj = new TrainSearchInfo();
		$is_have_from = $train_obj->getTrainTotal($station_from);
		$is_have_to = $train_obj->getTrainTotal($station_to);
			
		if ($is_have_from > 0)
		{//判断schedule表出发地中有该站点时，更新站点的省份/城市/站点名
			$this->db->clear();
			$this->db->addTable('schedule');
			$this->db->addValue('EnFromProvince',$newstation_arr['EnProvinceName']);
			$this->db->addValue('FromProvince',$newstation_arr['ProvinceName']);
			$this->db->addValue('EnFromCity',$newstation_arr['EnCityName']);
			$this->db->addValue('FromCity',$newstation_arr['CityName']);
			$this->db->addValue('EnFrom',$newstation_arr['EnStationName']);
			$this->db->addValue('From',$newstation_arr['StationName']);
			$this->db->addValue('FromWeight',$newstation_arr['Weight']);
			$this->db->addWhere('From', $orgstation_arr['StationName'], _ORM_OP_EQ);
			$this->db->update();
		}

		if ($is_have_to > 0)
		{//判断schedule表到达站中有该站点时，更新站点的省份/城市/站点名
			$this->db->clear();
			$this->db->addTable('schedule');
			$this->db->addValue('EnToProvince',$newstation_arr['EnProvinceName']);
			$this->db->addValue('ToProvince',$newstation_arr['ProvinceName']);
			$this->db->addValue('EnToCity',$newstation_arr['EnCityName']);
			$this->db->addValue('ToCity',$newstation_arr['CityName']);
			$this->db->addValue('EnTo',$newstation_arr['EnStationName']);
			$this->db->addValue('To',$newstation_arr['StationName']);
			$this->db->addValue('ToWeight',$newstation_arr['Weight']);
			$this->db->addWhere('To', $orgstation_arr['StationName'], _ORM_OP_EQ);
			$this->db->update();
		}
	}
	
	/**
	 * 删除 城市/站点 数据
	 * $orgstation_arr array 需要删除的数据的信息
	 */
	public function delStation($orgstation_arr)
	{
		$this->db->clear();
		$this->db->addTable('station');
		$this->db->addWhere('ID', $orgstation_arr['ID']);
		$success_flag = $this->db->delete();
		
		if ($success_flag)
		{
			//删除schedule表数据
			$this->db->clear();
			$this->db->addTable('schedule');
			$this->db->addWhere('From', $orgstation_arr['StationName']);
			$this->db->addWhere('To', $orgstation_arr['StationName'], _ORM_OP_EQ, _ORM_DT_AUTO, _ORM_OP_OR);
			$success_flag = $this->db->delete();
			
			if ($success_flag)
			{
				//需要写到日志里
				return false;
			}
			return true;
		}
		else 
		{
			//需要写到日志里
			return false;
		}
	}
}

?>