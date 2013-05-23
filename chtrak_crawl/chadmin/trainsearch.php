<?php
/**
 *  站站之间的采集和数据管理
 */

set_time_limit(0);
require_once ('../common.inc.php');

class trainsearch extends Action
{
	/**
	 * 采集页面
	 */
	public function doDefault()
	{
		global $app;
		$page = $app->page();
		$page->value('cur_left', 'trainsearch');
		
		$page->output();
	}
	
	/**
	 * 采集数据并入库
	 */
	public function doSearch()
	{
		global $app;
		$page = $app->page();
		$page->value('cur_left', 'trainsearch');
		
		$start = trim($_POST['start']);
		$end = trim($_POST['end']);
		$round = intval($_POST['round']);
		
		if ($start && $end)
		{
			importModule("Chadmin/TrainSearchInfo");
			$search_obj = new TrainSearchInfo();
			
			$result_1 = $search_obj->searchTrain($start, $end);
			
			//判断是否反向采集，采集返程
			$result_2 = true;
			if ($round == 1)
			{
				$result_2 = $search_obj->searchTrain($end, $start);
			}
			
			if ($result_1['status'] == false)
			{
				$info .= " 去程站点数据，". $result_1['msg'];
			}
			if ($result_2['status'] == false)
			{
				$info .= " 反向站点数据，". $result_1['msg'];;
			}
			
			$flag = 1;
			if ($info) // 采集出错，无直达车次或站点错误
			{
				$flag = 2;
			}
			else
			{
				$total = $result_1['total'] + $result_2['total'];
				$info = "数据采集成功，共" . $total . "条车次信息";
			}
			$page->value('info', $info);
			$page->value('flag', $flag);
			$page->value('href', "/chadmin/trainsearch.php?do=manage");
			$page->output("chadmin/tips.tpl");
		}
		else
		{
			$page->value('info', "请输入出发城市/站点 或 到达城市/站点");
			$page->value('flag', 2);
			$page->value('href', "/chadmin/trainsearch.php");
			$page->output("chadmin/tips.tpl");
		}
	}
	
	/**
	 * 采集站点的管理
	 */
	public function doManage()
	{
		global $app;
		$page = $app->page();
		$page->value('cur_left', 'trainsearch_manage');
		
		importModule("Chadmin.TrainSearchInfo");
		$search_obj = new TrainSearchInfo();
		
		$start_station = trim($_POST['start_station']);
		if (!$start_station)
		{
			$start_station = trim($_GET['start_station']);
		}
		$page->value('start_station', $start_station);
		$end_station = trim($_POST['end_station']);
		if (!$end_station)
		{
			$end_station = trim($_GET['end_station']);
		}
		$page->value('end_station', $end_station);
		$start_number = trim($_POST['start_number']);
		if (!$start_number)
		{
			$start_number = trim($_GET['start_number']);
		}
		$page->value('start_number', $start_number);
		$traintype = trim($_POST['traintype']);
		if (!$traintype)
		{
			$traintype = trim($_GET['traintype']);
		}
		$page->value('traintype', $traintype);
		$station_info = array();
		$station_info['start'] = $start_station;
		$station_info['end'] = $end_station;
		$station_info['number'] = $start_number;
		$station_info['traintype'] = $traintype;
		
		$total = $search_obj->getTrainTotal($station_info);
		if ($total > 0)
		{
			//翻页信息
			$pagesize = 10;
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
			$page_url = 'do=manage';
			if ($station_info['start'])
			{
				$page_url .= "&start_station=" . urlencode($station_info['start']);
			}
			if ($station_info['end'])
			{
				$page_url .= "&end_station=" . urlencode($station_info['end']);
			}
			if ($station_info['number'])
			{
				$page_url .= "&start_number=" . urlencode($station_info['number']);
			}
			if ($station_info['traintype'])
			{
				$page_url .= "&traintype=" . urlencode($station_info['traintype']);
			}
			$page_html = $page->getPageStr($total_page, $p, 'p', $page_url, false, 2);
			//分页
			$page->value('page_html', $page_html);
			
			$start = ($p-1) * $pagesize;
			$train_data = $search_obj->getTrainPage($start, $pagesize, $station_info);
			$page->value('train_data', $train_data);
		}
		
		$page->output('chadmin/trainsearch_manage.tpl');
	}
	
	/**
	 * 站站之间的车次信息单条修改页
	 */
	public function doEdit()
	{
		global $app;
		$page = $app->page();
		$page->value('cur_left', 'trainsearch_manage');
		
		$id = intval($_GET['id']);
		
		if(!$id)
		{
			$this->redirect($app->cfg['url']['root'] . "chadmin/trainsearch.php?do=manage");
		}
		
		importModule("Chadmin.TrainSearchInfo");
		$search_obj = new TrainSearchInfo();
		
		$train_info = $search_obj->getTrainInfo($id);
		$page->value('train_info', $train_info);
		
		$page->output('chadmin/trainsearch_edit.tpl');
	}
	
	/**
	 * 单条站站之间的车次信息修改入库
	 */
	public function doSave()
	{
		$id = intval($_POST['trainid']);
		
		if (!$id)
		{
			$this->redirect($app->cfg['url']['root'] . "chadmin/trainsearch.php?do=manage");
		}
		
		global $app;
		$page = $app->page();
		$page->value('cur_left', 'trainsearch_manage');
		
		$deptime = $_POST['deptime'];
		$arrtime = $_POST['arrtime'];
		$traveltime = $_POST['traveltime'];
		$distancekm = $_POST['distancekm'];
		$hardseat = $_POST['hardseat'];
		$softseat = $_POST['softseat'];
		$hardsleeper = $_POST['hardsleeper'];
		$softsleeper = $_POST['softsleeper'];
		$deluxesleeper = $_POST['deluxesleeper'];
		$businessclassseat = $_POST['businessclassseat'];
		$firstclassseat = $_POST['firstclassseat'];
		$secondclasseat = $_POST['secondclasseat'];
		$topclassseat = $_POST['topclassseat'];
		
		$train_info = array();
		if($deptime)
		$train_info['Deptime'] = $deptime;
		if($arrtime)
		$train_info['Arrtime'] = $arrtime;
		if($traveltime)
		$train_info['Traveltime'] = $traveltime;
		if($distancekm)
		$train_info['Distancekm'] = $distancekm;
		if($hardseat)
		$train_info['Hardseat'] = $hardseat;
		if($softseat)
		$train_info['Softseat'] = $softseat;
		if($hardsleeper)
		$train_info['Hardsleeper'] = $hardsleeper;
		if($softsleeper)
		$train_info['Softsleeper'] = $softsleeper;
		if($deluxesleeper)
		$train_info['Deluxesleeper'] = $deluxesleeper;
		if($businessclassseat)
		$train_info['Businessclassseat'] = $businessclassseat;
		if($firstclassseat)
		$train_info['Firstclassseat'] = $firstclassseat;
		if($secondclasseat)
		$train_info['Secondclasseat'] = $secondclasseat;
		if($topclassseat)
		$train_info['Topclassseat'] = $topclassseat;
		
		importModule("Chadmin.TrainSearchInfo");
		$search_obj = new TrainSearchInfo();
		
		$flag = $search_obj->setTrainInfo($id, $train_info);
		if($flag)
		{
			$info = "修改成功！";
		}
		else
		{
			$info = "修改失败！";
		}
		$page->value('info', $info);
		$page->value('href', "/chadmin/trainsearch.php?do=edit&id=" . $id);
		$page->output("chadmin/tips.tpl");
	}
	
	/**
	 * 删除单条站站之间的车次信息
	 */
	public function doDel()
	{
		$id = intval($_POST['id']);
		
		if (!$id)
		{
			$this->output(array('code' => 2, 'info' => '参数错误！'));
		}
		
		importModule("Chadmin/TrainSearchInfo");
		$search_obj = new TrainSearchInfo();
		
		$flag = $search_obj->delTrainInfo($id);
		if ($flag)
		{
			$this->output(array('code' => 1, 'info' => '删除成功！'));
		}
		else 
		{
			$this->output(array('code' => 2, 'info' => '删除失败！'));
		}
	}
	
	/**
	 * 保存售票设置
	 */
	public function doSaveSet()
	{
		$ids = trim($_POST['ids']);
		$set = trim($_POST['set']);
		
		if (!$ids || !$set){
			$this->output(array('code' => 2, 'info' => '参数错误！'));
		}
		
		$id_array = explode(",", $ids);
		$set_array = explode(",", $set);
		
		importModule("Chadmin/TrainSearchInfo");
		$search_obj = new TrainSearchInfo();
		
		foreach ($id_array as $key => $val)
		{
			$search_obj->saveTrainSet($val, $set_array[$key]);
		}
		
		$this->output(array('code' => 1, 'info' => '保存成功！'));
	}
}
$app->run();
?>