<?php
/**
 * 站站之间车次采集和管理的基类
 * @author lixiaoxin
 *
 */


class TrainSearchInfo
{
	private $db;
	
	public function __construct()
	{
		global $app;
		$this->db = $app->orm()->query();
	}
	
	/**
	 * 查询站站之间的数据条数
	 * @param array $station_info	查询条件
	 */
	public function getTrainTotal($station_info)
	{
		$sql = "SELECT COUNT(ID) FROM schedule WHERE 1=1";
		if ($station_info['start'])
		{
			$sql .= " AND (FromCity = '".$station_info['start']."' OR `From` = '".$station_info['start']."')";
		}
		if ($station_info['end'])
		{
			$sql .= " AND (ToCity = '".$station_info['end']."' OR `To` = '".$station_info['end']."')";
		}
		if ($station_info['number'])
		{
			$sql .= " AND Trainnumber like '%".$station_info['number']."%'";
		}
		if ($station_info['traintype'] == 1)
		{
			$sql .= " AND TrainType IN ('D', 'C', 'G')";
		}
		if ($station_info['traintype'] == 2)
		{
			$sql .= " AND TrainType NOT IN ('D', 'C', 'G')";
		}
		$total = $this->db->clear()->getValue($sql);
		
		return $total;
	}
	
	/**
	 * 查询单页的站站之间的车次信息
	 * @param int $start	起始条数
	 * @param int $limit	查询条数
	 * @param array $station_info	查询条件
	 */
	public function getTrainPage($start, $limit, $station_info = array())
	{
		$sql = "SELECT * FROM schedule WHERE 1=1";
		if ($station_info['start'])
		{
			$sql .= " AND (FromCity = '".$station_info['start']."' OR `From` = '".$station_info['start']."')";
		}
		if ($station_info['end'])
		{
			$sql .= " AND (ToCity = '".$station_info['end']."' OR `To` = '".$station_info['end']."')";
		}
		if ($station_info['number'])
		{
			$sql .= " AND Trainnumber like '%".$station_info['number']."%'";
		}
		if ($station_info['traintype'] == 1)
		{
			$sql .= " AND TrainType IN ('D', 'C', 'G')";
		}
		if ($station_info['traintype'] == 2)
		{
			$sql .= " AND TrainType NOT IN ('D', 'C', 'G')";
		}
		$sql .= " ORDER BY ID DESC LIMIT " . $start . "," . $limit;
		$result = $this->db->clear()->getArray($sql);
		
		return $result;
	}
	
	/**
	 * 根据ID获取站站之间的车次信息
	 * @param int $id 自动ID
	 */
	public function getTrainInfo($id)
	{
		$id = intval($id);
		if (!$id)
		{
			return false;
		}
		
		$sql = "SELECT * FROM schedule WHERE ID = '".$id."'";
		$result = $this->db->clear()->getRow($sql);
		
		return $result;
	}
	
	/**
	 * 修改站站之间的车次信息
	 * @param int $id 自动ID
	 * @param array $train_info 修改的信息
	 */
	public function setTrainInfo($id, $train_info)
	{
		if (!$id || !$train_info)
		{
			return false;
		}
		
		$this->db->clear();
		$this->db->addTable("schedule");
		$this->db->addWhere('id', $id);
		$flag = $this->db->update($train_info);
		
		return $flag;
	}
	
	/**
	 * 删除站站之间的车次信息
	 * @param int $id
	 */
	public function delTrainInfo($id)
	{
		if (!$id)
		{
			return false;
		}
		
		$this->db->clear();
		$this->db->addTable("schedule");
		$this->db->addWhere('id', $id);
		$flag = $this->db->delete();
		
		return $flag;
	}
	
	public function searchTrain($start, $end)
	{
		//初始化采集类
		importModule("Chadmin.CrawlWebContent");
		$crawl_obj = new CrawlWebContent();
		
		//采集单程
		$result = $crawl_obj->getData($start, $end);
		
		if ($result == false)
		{
			$search_data = array();
			$search_data['status'] = false;
			$search_data['msg'] = "站点输入错误或没有车次信息.";
			return $search_data;
		}
		
		$flag = false;
		$i = 0;
		if ($result && is_array($result))
		{
			$flag = true;
			importModule("Chadmin.ManageStationInfo");
			$station_obj = new ManageStationInfo();
			
			$site_array = array('Hardseat', 'Softseat', 'Hardsleeper', 'Softsleeper', 'Deluxesleeper', 'Businessclassseat', 'Firstclassseat', 'Secondclasseat', 'Topclassseat');
			$time = date('Y-m-d H:i:s');
			
			foreach ($result as $key => $val)
			{
				if ($val['Trainnumber'] && $val['From'] && $val['To'])
				{
					$val['Trainnumber'] = trim($val['Trainnumber']);
					$val['TrainType'] = substr($val['Trainnumber'], 0, 1);
					
					$this->getTrainLine($val['Trainnumber'], $val['TrainType']);
					
					$val['From'] = trim($val['From']);
					$val['To'] = trim($val['To']);
					//获得站点的信息，存入到站站之间的车次信息表中
					$from_info = $station_obj->getStationByName($val['From'], 'insert');
					if ($from_info)
					{
						$val['EnFromProvince'] = $from_info['EnProvinceName'];
						$val['FromProvince'] = $from_info['ProvinceName'];
						$val['EnFromCity'] = $from_info['EnCityName'];
						$val['FromCity'] = $from_info['CityName'];	
						$val['EnFrom'] = $from_info['EnStationName'];	
						$val['FromWeight'] = $from_info['Weight'];
					}
					$to_info = $station_obj->getStationByName($val['To'], 'insert');
					if ($to_info)
					{
						$val['EnToProvince'] = $to_info['EnProvinceName'];
						$val['ToProvince'] = $to_info['ProvinceName'];
						$val['EnToCity	'] = $to_info['EnCityName'];
						$val['ToCity'] = $to_info['CityName'];	
						$val['EnTo'] = $to_info['EnStationName'];	
						$val['ToWeight'] = $to_info['Weight'];
					}
					
					//查询该站站之间的车次是否存在
					$sql = "SELECT ID FROM schedule WHERE Trainnumber = '".$val['Trainnumber']."' AND `From` = '".$val['From']."' AND `To` = '".$val['To']."'";
					$schedule_info = array();
					$schedule_info = $this->db->clear()->getRow($sql);
					
					$this->db->clear();
					$this->db->addTable('schedule');
					if ($schedule_info['ID'])
					{
						$upd_flag = false;
						$this->db->addWhere('ID', $schedule_info['ID']);
						foreach ($site_array as $key)
						{
							if (!isset($val[$key]))
							{
								$val[$key] = 0;
							}
						}
						$val['CreateTime'] = $time;
						$val['OnSale'] = 1;
						$upd_flag = $this->db->update($val);
						if (!$upd_flag)
						{
							$data = $val;
							$data['error_info'] = "修改失败";
							chtrak_log('Search/Train/error', $data);
							$flag = false;
						}
					}
					else 
					{
						$this->db->insert($val);
						$ID = 0;
						$ID = $this->db->getLastId();
						if (!ID)
						{
							$data = $val;
							$data['error_info'] = "入库失败";
							chtrak_log('Search/Train/error', $data);
							$flag = false;
						}
					}
					$i++;
				}
				else
				{
					$data = $val;
					$data['error_info'] = "采集的数据错误";
					chtrak_log('Search/Train/error', $data);
					$flag = false;
				}
			}
		}
		
		if ($flag == true)
		{
			$search_data = array();
			$search_data['status'] = true;
			$search_data['msg'] = "数据采集成功！";
			$search_data['total'] = $i;
		}
		else
		{
			$search_data = array();
			$search_data['status'] = false;
			$search_data['msg'] = "数据采集失败！";
		}
		
		return $search_data;
	}
	
	public function getTrainLine($trainnumber, $traintype)
	{
		$this->db->clear();
		$this->db->addTable('TrainLine');
		$this->db->addField("*");
		$this->db->addWhere("TrainNum", $trainnumber);
		$result = $this->db->getRow();
		if(!$result['Id'])
		{
			$this->db->clear();
			$this->db->addTable('TrainLine');
			$this->db->addValue("TrainNum", $trainnumber);
			$this->db->addValue("TrainType", $traintype);
			$this->db->insert();
			$id = $this->db->getLastId();
			if (!id)
			{
				$data = array();
				$data['number'] = $trainnumber;
				$data['type'] = $traintype;
				$data['info'] = "插入失败！";
				chtrak_log('Search/Train/insert', $data);
			}
		} 
	}
	
	public function saveTrainSet($id, $onsale)
	{
		if (!$id)
		{
			return false;
		}
		
		$sql = "UPDATE schedule SET OnSale = '".$onsale."' WHERE ID = '".$id."'";
		$result = $this->db->clear()->exec($sql);
		if (!$result)
		{
			$data = array();
			$data['id'] = $id;
			$data['onsale'] = $onsale;
			chtrak_log('Search/Train/update', $data);
		}
	}
}

?>