<?php
/*
 *  城市站点管理
 * @author RickieL
 */
class AutoCrawlTrain
{
	private $db;

	public function __construct()
	{
		global $app;
		$this->db = $app->orm()->query();
	}

	
	/**
	 * 车次是否存在，或是否已更新
	 * $TrainNum  string  车次信息   
	 * return $craw_flag   返回是否抓取需要抓取的标志   
	 */
	public function getTrainState($TrainNum = '')
	{
		if (!$TrainNum)
		{
			echo "num";
			return false;
		}
		
		$this->db->clear();
		$this->db->addTable("TrainLine");
		$this->db->addWhere('TrainNum', $TrainNum);
		$line_row = $this->db->getRow();
		if ($line_row['IsUpdate'] == 1)
		{
			return false;
		}
		
		return true;
		
	}
	
	
	/**
	 * 更新TrainLine信息
	 * $TrainLine  array  车次信息
	 */
	public function getUpdateLine($TrainNum, $TrainLine = array())
	{
		if (!$TrainNum || !$TrainLine)
		{
			return false;
		}
		
		$this->db->clear();
		$this->db->addTable("TrainLine");
		$this->db->addWhere('TrainNum', $TrainNum);
		$flag = $this->db->update($TrainLine);
		
		return $flag;
	}
	
	/**
	 * 获取 TrainLine
	 * $i int   一直累加，直至取到数据
	 */
	public function getTrainLine($i = 0)
	{
		$sql = "SELECT * FROM TrainLine WHERE IsUpdate=0 ORDER BY ID DESC ";
		$result = $this->db->clear()->getRow($sql);
		if ($result['TrainNum'])
		{
			return $result['TrainNum'];
		}
		return false;
	}
}

?>