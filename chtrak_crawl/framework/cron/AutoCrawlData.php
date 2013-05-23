<?php

set_time_limit(0);

require_once ('../../common.inc.php');
define("NO_CHECK_LOGIN", true);
class AutoCrawlData extends Action
{
	public function doDefault()
	{
		global $app;
		$this->db = $app->orm()->query();

		importModule('chadmin/CrawlWebContent');
		$c_craw = new CrawlWebContent();
		importModule('chadmin/AutoCrawlTrain');
		$c_auto = new AutoCrawlTrain();

		importModule('chadmin/TrainSearchInfo');
		$c_search = new TrainSearchInfo();

		//获取第一个未更新的车次号
		$line_num = 0;
		if ($c_auto->getTrainLine())
		{
			$line_num = $c_auto->getTrainLine();
		}
		
		//如果该次列车需要更新才更新
		if ($c_auto->getTrainState($line_num))
		{
			$trainline = $c_craw->getTrainLineData($line_num);
		}

		//对数据入库
		if ($trainline)
		{
			//需更新的车次信息
			$train_update_info = array();
			$train_update_info['DepPlace'] = $trainline['DepPlace'];
			$train_update_info['ArrPlace'] = $trainline['ArrPlace'];
			$train_update_info['DepTime'] = $trainline['DepTime'] . "00";
			$train_update_info['ArrTime'] = $trainline['ArrTime'] . "00";
			$train_update_info['Traveltime'] = $trainline['Traveltime'];
			$train_update_info['DistanceKm'] = $trainline['DistanceKm'];
			$train_update_info['CreateTime'] = $trainline['CreateTime'];
			$train_update_info['Stations'] = $trainline['Stations'];
			$train_update_info['IsUpdate'] = 1;
				
			$up_result = $c_auto->getUpdateLine($trainline['TrainNum'], $train_update_info);
			if (!$up_result)
			{
				$err_data = $train_update_info;
				$err_data['error_info'] = "修改失败";
				chtrak_log('Search/Train/error', $err_data);
				exit;
			}
				
		


			//to do 将车次的站点两两组合，并查询经过该两站点的车次信息
			$count = 0;
			foreach ($trainline['Station_arr'] as $key=>$val)
			{
				foreach ($trainline['Station_arr'] as $k=>$v)
				{
					if ($val['Station'] != $v['Station'])
					{
						//检查该两站点之间是否已经有数据，若有，则跳过采集
						$station_info['start'] = $val['Station'];
						$station_info['end'] = $v['Station'];
						$line_count = $c_search->getTrainTotal($station_info);
						if (!$line_count)
						{
							//采集
							$station_flag = $c_search->searchTrain($station_info['start'], $station_info['end']);
							//判断是否采集成功
							if (!$station_flag['status'])
							{
								$err_data = array();
								$err_data = $station_info;
								$err_data['error_info'] = "采集失败";
								chtrak_log('Search/Train/error', $err_data);
							}
	
							$sleep_time = mt_rand(40, 80);
							sleep($sleep_time);
						}
					}
				}
			}
		}
	}
}

$app->run();