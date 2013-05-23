<?php 


class CrawlWebContent
{
	var $WebRawContent		=	'';					// 网页的原始内容
	var $Refer = 'http://www.17u.cn/train/';             // 设置refer
	var $UserAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94';  //默认user-agent
	
	var $page_count = 1;                            // 返回的页面页数
	var $url = '';                                  // 拼凑后的存储的 url
	
	/**
	 * 根据出发和到达地址抓取页面数据
	 * $url  string  url地址
	 */
	public function getWebRawContent($url)
	{
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_ENCODING, "gzip" );         //设置为客户端支持gzip压缩
		curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 30 );
		curl_setopt($ch2, CURLOPT_USERAGENT,  $this->UserAgent);
		curl_setopt($ch2, CURLOPT_REFERER, $this->Refer );
		curl_setopt($ch2, CURLOPT_URL, $url);
		curl_setopt($ch2, CURLOPT_HEADER, false);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
		$WebRawContent = curl_exec($ch2);
		curl_close($ch2);
		$WebRawContent = str_replace("\r\n","",$WebRawContent);
		return $WebRawContent;
	}
	
	/**
	 * 获取总页数
	 * $url  string  url地址
	 */
	public function getPageCount($content)
	{
		preg_match('/共(\d+)页<\/span>/s', $content, $matches);   //s表示匹配换行符
		if ($matches[1])
		{
			$page_count = $matches[1];
		}
		else 
		{
			$page_count = 1;
		}
		
		return $page_count;
	}
	
	/**
	 * 拼凑url
	 * $start  string  出发站
	 * $end  string  到达站
	 * $p  int  页码数
	 */
	public function getUrl($start, $end, $p = 1)
	{
		$this->url = '';
		$url_start = urlencode($start);
		$url_end = urlencode($end);
		
		$url_head = 'http://www.17u.cn/train/trainstationtostationresult_';
		$url_place = $url_start . "_" . $url_end . "_";
		$url_page = $p;
		$url_back = '.html';
		
		$this->url = $url_head . $url_place . $url_page . $url_back;
		
		return $this->url;
	}
	
	/**
	 * 拼凑url
	 * $trainline  string  车次
	 */
	public function getUrlTrainline($trainline)
	{
		$trainline = urlencode(trim($trainline));
	
		$url_head = 'http://www.17u.cn/train/trainsearchbytrainline_';
		$url_line = $trainline;
		$url_back = '.html';
	
		$url = $url_head . $url_line . $url_back;
	
		return $url;
	}
	
	/**
	 * 获取 需要的数据
	 * $content  string  网页内容
	 */
	public function getOnePageData($content)
	{
		$Seat_Normal_arr = array(	'硬座' => 'Hardseat',
				'软座' => 'Softseat',
				'硬卧下' => 'Hardsleeper',
				'软卧下' => 'Softsleeper',
				'豪华软卧' => 'Deluxesleeper',
				'商务座' => 'Businessclassseat',
				'一等座' => 'Firstclassseat',
				'二等座' => 'Secondclasseat',
				'头等仓' => 'Topclassseat',
		);
		
		$Seat_Special_arr = array(	'硬座' => 'Secondclasseat',
				'软座' => 'Firstclassseat',
				'硬卧下' => 'Hardsleeper',
				'软卧下' => 'Softsleeper',
				'豪华软卧' => 'Deluxesleeper',
				'商务座' => 'Businessclassseat',
				'一等座' => 'Firstclassseat',
				'二等座' => 'Secondclasseat',
				'头等仓' => 'Topclassseat',
		);
		
		//当前页的列车数
		preg_match_all('@<tr>(.*)</tr>@Us', $content, $m);   // U表示非贪婪方式匹配
		
		foreach ($m[1] as $key=>$val)
		{
			if ($key > 0)
			{
				$val = preg_replace("/\\s/", '', $val);   //去掉所有空白
				preg_match_all('@<td[^>]+>(.*)</td>@Us', $val, $train_tmp);
				$train_arr = array();
				//获取车次
				preg_match_all('@<a[^>]+>(.*)</a>@U', $train_tmp[1][0], $train_num_m);
				$train_arr['Trainnumber'] =  $train_num_m[1][0];
				$FirstCharacter = strtoupper(substr( $train_arr['Trainnumber'], 0, 1 ));
		
				//获取出发地和到达地
				preg_match_all('@<a[^>]+>(.*)</a>@U', $train_tmp[1][1], $train_num_m);
				$train_arr['From'] =  $train_num_m[1][0];
				$train_arr['To'] =  $train_num_m[1][1];
		
				preg_match_all('@<span>(.*)</span><br/><font>(.*)</font>@U', $train_tmp[1][2], $train_num_m);
				$train_arr['Arrtime'] =  $train_num_m[2][0];
				$train_arr['Deptime'] =  $train_num_m[1][0];
		
				$train_arr['Traveltime'] = $train_tmp[1][3];
				if ($train_tmp[1][3])
				{
					$time_arr = explode("小时", $train_tmp[1][3]);
					$time_count = count($time_arr);
					if ($time_count == 2)
					{
						$train_arr['Traveltime'] = intval($time_arr[0]) * 60 + intval($time_arr[1]);
					}
					else
					{
						$train_arr['Traveltime'] = intval($time_arr[0]);
					}
				}
		
				$train_arr['Distancekm'] = $train_tmp[1][4];
		
				preg_match_all('@([\D]+)([\d]+)@', $train_tmp[1][5], $train_num_m, PREG_SET_ORDER);
				foreach ($train_num_m as $pk=>$pv)
				{
					if (in_array($FirstCharacter, array('G', 'D')))
					{
						if (isset($Seat_Special_arr[$pv[1]]))
						{
							$train_arr[$Seat_Special_arr[$pv[1]]] = $pv[2];
						}
						else
						{
							$train_arr['Secondclassseat'] = $pv[2];
						}
					}
					else
					{
						if (isset($Seat_Normal_arr[$pv[1]]))
						{
							$train_arr[$Seat_Normal_arr[$pv[1]]] = $pv[2];
						}
						else
						{
							$train_arr['Softseat'] = $pv[2];
						}
					}
				}
		
				$train_info[] = $train_arr;
			}
		}
		
		return $train_info;
	}
	
	/**
	 * 接口函数和最后返回的数据函数
	 * $start  string  出发站
	 * $end  string  到达站
	 */
	public function getData($start, $end)
	{
		$default_url = $this->getUrl($start, $end);
		$default_RawContent = $this->getWebRawContent($default_url);
		
		if (preg_match('/系统返回查询首页/', $default_RawContent))
		{
			$data = array();
			$data['error'] = $start."→".$end."没有车次信息，输入的站点可能错误！";
			chtrak_log("Crawl/error", $data);
			return false;
		}
		elseif (preg_match('/您查询的(.*)到(.*)无直达火车/U', $default_RawContent))
		{
			$data = array();
			$data['error'] = $start."→".$end."无直达火车！";
			chtrak_log("Crawl/error", $data);
			return false;
		}
		
		$page_count = $this->getPageCount($default_RawContent);
		
		$train_info = $this->getOnePageData($default_RawContent);
		
		$train_data = array();
		if ($train_info && is_array($train_info))
		{
			foreach ($train_info as $key => $val)
			{
				$train_data[] = $val;
			}
		}

		// 多于1页的情况
		for ($i=2; $i <= $page_count; $i++)
		{
			$result = array();
			$default_url = $this->getUrl($start, $end, $i);
			$default_RawContent = $this->getWebRawContent($default_url);
			$result = $this->getOnePageData($default_RawContent);
			if ($train_info && is_array($train_info))
			{
				foreach ($result as $key => $val)
				{
					$train_data[] = $val;
				}
			}
		}
		
		return $train_data;
	}
	
	/**
	 * 获取火车车次页面的信息
	 * $line  string  车次
	 */
	public function getTrainLineData($line)
	{
		$trainline = array();
		$trainline['Station_arr'] = array();
		$trainline['Stations'] = '';
		$trainline['TrainNum'] = trim($line);
		$time = date('Y-m-d H:i:s');
		$trainline['CreateTime'] = $time;
		
		/**
		 * 初始化站点类
		 */
		importModule('chadmin/ManageStationInfo');
		$c_station = new ManageStationInfo();
		
		/**
		 *  获取网页全部内容
		 */
		$url =  $this->getUrlTrainline($trainline['TrainNum']);
		$content = $this->getWebRawContent($url);
		
		// 判断内容是否存在
		if (preg_match('/对不起，您查询的信息不存在！/', $content))
		{
			$data = array();
			$data['error'] = " 没有该车次 $line 信息！";
			chtrak_log("Crawl/error", $data);
			return false;
		}
		/**
		*  获取车次主体信息
		 */
		preg_match_all('@<table class="checi">.*<tr>.*</tr>.*<tr>(.*)</tr>.*</table>@Us', $content, $train_tr);
		preg_match_all('@<td[^>]+>(.*)</td>@Us', $train_tr[1][0], $train_td);
		//起始站
		preg_match_all('@<a[^>]+>(.*)<br />(.*)</a>@Us', $train_td[1][1], $train_place);
		$trainline['DepPlace_name'] =  trim($train_place[1][0]);
		$Station_start = $c_station->getStationByName($trainline['DepPlace_name'], 'insert');
		$trainline['StationId'] = 0;
		if ($trainline['DepPlace_name'])
		{
			$trainline['DepPlace'] = $Station_start['ID'];
		}
		
		$trainline['ArrPlace_name'] =  trim($train_place[2][0]);
		$Station_end = $c_station->getStationByName($trainline['ArrPlace_name'], 'insert');
		$trainline['StationId'] = 0;
		if ($trainline['ArrPlace_name'])
		{
			$trainline['ArrPlace'] = $Station_end['ID'];
		}
		
		//起始时间
		preg_match_all('@<span>(.*)</span><br /><font>(.*)</font>@Us', $train_td[1][2], $train_time);
		$trainline['DepTime'] =  trim($train_time[1][0]);
		$trainline['ArrTime'] =  trim($train_time[2][0]);
		//运行时间
		$trainline['Traveltime'] = $train_td[1][3];
		if ($train_td[1][3])
		{
			$time_arr = explode("小时", $train_td[1][3]);
			$time_count = count($time_arr);
			if ($time_count == 2)
			{
				$trainline['Traveltime'] = intval($time_arr[0]) * 60 + intval($time_arr[1]);
			}
			else
			{
				$trainline['Traveltime'] = intval($time_arr[0]);
			}
		}
		$trainline['DistanceKm'] = intval($train_td[1][4]);
		
		/**
		*  获取车次的站点信息
		*/
		preg_match_all('@<table class=\'checi_info\'>(.*)</table>@Us', $content, $train_table);
		preg_match_all('@<tr>(.*)</tr>@Us', $train_table[1][0], $train_tr);
		foreach ($train_tr[1] as $key=>$val)
		{
			if ($key > 0)
			{
				/*
				*  单个站点的信息数组
				*  $LineStation_arr = （站点序号，站点名，站点ID，出发时间，到达时间，停留时间，运行时间，里程）
				*/
				$LineStation_arr = array();
			
				preg_match_all('@<td[^>]+>(.*)</td>@Us', $val, $train_td);
				//站点顺序号
				$LineStation_arr['Sequence'] = trim($train_td[1][0]);
				//站点名  站点ID
				preg_match_all('@<a[^>]+>(.*)</a>@Us', $train_td[1][1], $train_a);
				$LineStation_arr['Station'] = trim($train_a[1][0]);
				$Station_info = $c_station->getStationByName($LineStation_arr['Station'], 'insert');
				$LineStation_arr['StationId'] = 0;
				if ($Station_info)
				{
					$LineStation_arr['StationId'] = $Station_info['ID'];
				}
				//出发时间   到达时间
				$LineStation_arr['DepTime'] = trim($train_td[1][3]);
				$LineStation_arr['ArrTime'] = trim($train_td[1][4]);
				//停留时间   运行时间
				$LineStation_arr['StayTime'] = intval($train_td[1][5]);
				$LineStation_arr['TravelTime'] = 0;
				if ($train_td[1][6])
				{
					$time_arr = explode("小时", $train_td[1][6]);
					$time_count = count($time_arr);
					if ($time_count == 2)
					{
						$LineStation_arr['Traveltime'] = intval($time_arr[0]) * 60 + intval($time_arr[1]);
					}
					else
					{
						$LineStation_arr['Traveltime'] = intval($time_arr[0]);
					}
				}
			
				//if ($LineStation_arr['StayTime'] == "－") $LineStation_arr['StayTime'] = 0;
				//里程
				$LineStation_arr['DistanceKm'] = intval($train_td[1][7]);
				$trainline['Station_arr'][] = $LineStation_arr;
			}
		}
		$trainline['Stations'] = serialize($trainline['Station_arr']);
		return $trainline;
	}
	
	
}

?>