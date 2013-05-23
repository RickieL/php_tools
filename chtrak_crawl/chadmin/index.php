<?php
/**
 *  后台首页
 */
set_time_limit(0);

require_once ('../common.inc.php');
define("NO_CHECK_LOGIN", true);
class index extends Action
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
		
		//如果该次列车需要更新才更新
		if ($c_auto->getTrainState('K9037'))
		{
			$trainline = $c_craw->getTrainLineData('K9037');
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
			
		}
		
		
		//to do 将车次的站点两两组合，并查询经过该两站点的车次信息
		$count = 0;
		foreach ($trainline['Station_arr'] as $key=>$val)
		{
			foreach ($trainline['Station_arr'] as $k=>$v)
			{
				if ($val['Station'] != $v['Station'])
				{
					echo $count++;
					
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
	
	
	/**
	 * 用户列表
	 */
	public function test()
	{
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_ENCODING, "gzip" );         //设置为客户端支持gzip压缩
		curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 30 );
		curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94' );
		curl_setopt($ch2, CURLOPT_REFERER, 'http://www.u17.com/' );
		curl_setopt($ch2, CURLOPT_URL, 'http://www.17u.cn/train/trainstationtostationresult_%E6%AD%A6%E6%B1%89_%E5%B9%BF%E5%B7%9E_1.html');
		//curl_setopt($ch2, CURLOPT_URL, 'http://yf.chtrak.com/');
		curl_setopt($ch2, CURLOPT_HEADER, false);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
		//获取原始网页内容
		$content = curl_exec($ch2);
		
		$content = str_replace("\r\n","",$content);    //去除换行符
		
		//获取总页数
		preg_match('@<div[^>]+divResult[^>]+>(.*)共(\d+)页</span>@s', $content, $matches);   //s表示匹配换行符
		$page_count = $matches[2];
		
		//当前页的列车数
		preg_match_all('@<tr>(.*)</tr>@Us', $matches[1], $m);   // U表示非贪婪方式匹配
		$page_train_count = count($m[1]);
		
		$Seat_Normal_arr = array(	'硬座' => 'Hardseat',
				'软座' => 'Softseat',
				'硬卧下' => 'Hardsleeper',
				'软卧下' => 'Softsleeper',
				'豪华软卧' => 'Deluxesleeper',
				'商务座' => 'Businessclassseat',
				'一等座' => 'Firstclassseat',
				'二等座' => 'Secondclassseat',
				'头等仓' => 'Secondclassseat',
		);
		
		$Seat_Special_arr = array(	'硬座' => 'Firstclassseat',
				'软座' => 'Secondclassseat',
				'硬卧下' => 'Hardsleeper',
				'软卧下' => 'Softsleeper',
				'豪华软卧' => 'Deluxesleeper',
				'商务座' => 'Businessclassseat',
				'一等座' => 'Firstclassseat',
				'二等座' => 'Secondclassseat',
				'头等仓' => 'Secondclassseat',
		);
		
		foreach ($m[1] as $key=>$val)
		{
			if ($key > 0)
			{
				$val = preg_replace("/\\s/", '', $val);   //去掉所有空白
				//preg_match_all('@<td>(\s<a[^>]+>\s)?(.*)(\s<\/a>\s)?</td>@Us', $val, $train_tmp);
				preg_match_all('@<td[^>]+>(.*)</td>@Us', $val, $train_tmp);
				$train_arr = array();
				//获取车次
				preg_match_all('@<a[^>]+>(.*)</a>@U', $train_tmp[1][0], $train_num_m);
				$train_arr['Trainnumber'] =  $train_num_m[1][0];
				$train_arr['FirstCharacter'] = strtoupper(substr( $train_arr['Trainnumber'], 0, 1 ));
				
				//获取出发地和到达地
				preg_match_all('@<a[^>]+>(.*)</a>@U', $train_tmp[1][1], $train_num_m);
				$train_arr['From'] =  $train_num_m[1][0];
				$train_arr['To'] =  $train_num_m[1][1];
				
				preg_match_all('@<span>(.*)</span><br/><font>(.*)</font>@U', $train_tmp[1][2], $train_num_m);
				$train_arr['Arrtime'] =  $train_num_m[2][0];
				$train_arr['Deptime'] =  $train_num_m[1][0];
				
				$train_arr['Traveltime'] = $train_tmp[1][3];
				$train_arr['Distancekm'] = $train_tmp[1][4];
				$train_arr['Price'] = $train_tmp[1][5];
				
				preg_match_all('@([\D]+)([\d]+)@', $train_tmp[1][5], $train_num_m, PREG_SET_ORDER);
				foreach ($train_num_m as $pk=>$pv)
				{
					if ($train_arr['FirstCharacter'] == 'G' or $train_arr['FirstCharacter'] == 'D')
					{
						$train_arr[$Seat_Special_arr[$pv[1]]] = $pv[2];
					}
					else 
					{
						$train_arr[$Seat_Normal_arr[$pv[1]]] = $pv[2];
					}
				}
				
				var_dump($train_arr);exit;
				$train_info[] = array();
			}
		}
		
	}
}
$app->run();

?>