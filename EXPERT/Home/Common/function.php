<?php
	function deleteEmptyValue(&$param){
	    foreach ($param as $key => $value) {
	        if ($value == '')
	            unset($param[$key]);
	    }
	}

//	function exportExcel($filename, $title = null, $data = null){
//		header('Content-Type: application/vnd.ms-excel');
//		header('Content-Disposition: attachment; filename='.$filename.'.xls');
//		header('Pragma: no-cache');
//		header('Expires: 0');
//		echo iconv('utf-8', 'gbk', implode("\t", $title)), "\n";
//		foreach ($data as $value){
//			echo iconv('utf-8', 'gbk', implode("\t", array_values($value))),"\n";
//		}
//	}

/*
 * exportExcel方法：
 * 入参：filename:文件名
 * 		field：字段名，与D字段对应
 * 		data：数据
 * 		title:表头名，可以为中文,控制器根据titlemap获得
 */

	function exportExcel($filename,$field,$data,$title=null ){
		if($title==''){
			$title = $field;
		}
/*		$filename = iconv( 'gb2312','utf-8', $filename);//将文件名称从gb2312转到utf-8
		$fileName = $_SESSION['account'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
*/
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$filename.'.xls"');
		header("Content-Disposition:attachment;filename=$filename.xls");//attachment新窗口打印inline本窗口打印

		$cellNum = count($field);
		$dataNum = count($data);
		vendor("EXCEL.PHPExcel");

		$objPHPExcel = new PHPExcel();
		$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

/*		$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
*/
		for($i=0;$i<$cellNum;$i++){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $title[$i]);
		}
		// Miscellaneous glyphs, UTF-8
		for($i=0;$i<$dataNum;$i++){
			for($j=0;$j<$cellNum;$j++){
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $data[$i][$field[$j]]);
			}
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}


/**
 * 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
 * 将Excel文件转化为数组
 */
function readExcel($filename, $extension='xls', $encode = 'utf-8')
{
	vendor("EXCEL.PHPExcel");

	if ($extension == "xls") {
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	} else if ($extension == "xlsx") {
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	}
	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($filename);
	$objWorksheet = $objPHPExcel->getActiveSheet();
	$highestRow = $objWorksheet->getHighestRow();
	$highestColumn = $objWorksheet->getHighestColumn();
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	$firstline = 2;//默认有表头，需要跳过第一行的表头
	$excelData = array();
	for ($row = $firstline; $row <= $highestRow; $row++) {
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			/*实现：日期格式需要在Excel中就转化为text的1991-10-09形式*/
			$excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			/*未实现：由于PHPExcel读入日期的时候有错误，因此需要手动判断是否是日期格式
			$cell =$objWorksheet->getCellByColumnAndRow($col, $row);
			$value=$cell->getValue();
			if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
				$cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();
				$formatcode=$cellstyleformat->getFormatCode();
				if (preg_match('/^(\d{4})/(0\d{1}|1[0-2])/(0\d{1}|[12]\d{1}|3[01])$/', $formatcode)) {
					$excelData[$row][]=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
				}else{
//					$excelData[$row][]=PHPExcel_Style_NumberFormat::toFormattedString($value,$formatcode);
					$excelData[$row][]=(string)$value;
				}
			}
			*/

		}
	}
	return $excelData;
}


	function objectToArray($object)
	{
	    $_array = is_object($object) ? get_object_vars($object) : $object;
	    foreach ($_array as $key => $value) {
	        $value = (is_array($value) || is_object($value)) ? objectToArray($value) : $value;
	        $array[$key] = $value;
	    }
	    return $array;
	}

	function getIp(){
		if(getenv('HTTP_CLIENT_IP')) { 
			$ip = getenv('HTTP_CLIENT_IP');
		} 
		else if(getenv('HTTP_X_FORWARDED_FOR')) 
		{ 
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} 
		else if(getenv('REMOTE_ADDR')) 
		{ 
			$ip = getenv('REMOTE_ADDR');
		} else 
		{ 
			$ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		}
		return $ip;
	}


/*
 * 输入：
 * $i_data,需要插入外表的真实数据
 * $i_table,外表的名称
 * 返回值：
 * 获得的外表的id
 */
function getForeignKeyFromDB($i_data,$i_table){
	if($i_data!=null){
		$t_id = null;
			//缓存中不存在当前的学历
//			$condition["name"]=$i_data;
			$d_array = M($i_table)->where("name='%s' ",$i_data)->field('id')->find();
			$t_id = (int)$d_array['id'];
		if($t_id!=null){
			return  $t_id;
		}
	}
	//外键获取失败
//  $this->error ( $i_table.' 的外键id获取失败，无法插入' );
	return -1;
}


/*
 * 输入：
 * $i_data,需要插入外表的真实数据
 * $i_table,外表的名称
 * $i_buffer=null，外表数据缓存
 * 返回值：
 * 	1.正常数据，返回获得的外表的id，一般大于0
 *  2.输入的是空数据，无法获得外表id，返回0
 *  3.输入的是异常的名字，数据库中不存在这种外键，返回-1
 */
function getForeignKey($i_data,$i_table,&$i_buffer=null){
	if($i_data!=null){
		$t_id = null;
		if(array_key_exists($i_data,$i_buffer)){
			//缓存中存在当前的学历，取出来type_id，插入到person表中
			$t_id = $i_buffer[$i_data];
		}else{
			//缓存中不存在当前的学历
//			$condition["name"]=$i_data;
			$d_array = M($i_table)->where("name='%s' ",$i_data)->field('id')->find();
			$t_id = (int)$d_array['id'];
		}
		if($t_id!=null){
			if($i_buffer!=null){
				$i_buffer[$i_data]=$t_id;//加入缓存
			}
			return  $t_id;
		}else{
			//$d_id获取失败，无法插入
//                $this->error ( $i_table.' 的id获取失败，无法插入' );
			return -1;
		}
	}
	return 0;//返回0代表data数据为空
}

function getHonorIdByName($i_data){
	if($i_data!=null){
		$t_id = null;
			//缓存中不存在当前的学历
//			$condition["name"]=$i_data;
			$d_array = M("academic_honor")->where("name='%s' or abbr_name='%s' ",$i_data,$i_data)->field('id')->find();
			$t_id = (int)$d_array['id'];
		if($t_id!=null){
			return  $t_id;
		}else{
			//$d_id获取失败，无法插入
			return -1;
		}
	}
	return 0;//返回0代表data数据为空
}

function getPersonIdByEmployeeNo($employee_no)
{
	if ($employee_no != null) {
		$id = null;
		$p = M("person")->where("employee_no='%s' ", $employee_no)->field('id')->find();
		$id = (int)$p['id'];

		if ($id != null) {
			return $id;
		} else {
			//$d_id获取失败，无法插入
			return -1;
		}
	}
}

/*
 * 输入：教职工姓名,学院名
 * 处理：首先搜索名字，若只有一条数据则返回id，
 * 		否则存在重名现象，此时，根据学院信息进行二次筛选
 * 隐含：学院曾用名记录
 * 输出：
 * 		成功：返回人员的id
 * 		失败：返回-1
 * 		用户名为空：返回0
 */
function getPidByNameAndCollege($employee_name,$college_id)
{
	$id = null;
	$equ_colleges = array(array('7','12'));//  (计控=软件学院)
	if ($employee_name != null) {
		$p = D("person")->where("name='%s' ", $employee_name)->select();
		$dupNum = count($p);
		if($dupNum==1){//单例情况，直接返回id值
			$id = (int)$p[0]['id'];
		}else{
			if($dupNum>1 && $college_id>0){
				//存在重名，可以使用学院名进行区分
				foreach($p as $item){
					if($item['college_id']==$college_id){
						$id = (int)$item['id'];
					}else{//寻找等价学院
						foreach ($equ_colleges as $ecollege){
							if(in_array($college_id,$ecollege)){
								foreach ($ecollege as $cid){
									if($item['college_id']==$cid){
										$id = (int)$item['id'];
										break 2;//结束等价学院的搜索
									}
								}
							}
						}
					}
				}
			}
		}
	}
	if ($id != null) {
		return $id;
	}else{
		return -1;
	}

}


/**
 * 字符串半角和全角间相互转换
 * @param string $str 待转换的字符串
 * @param int  $type TODBC:转换为全角；TOSBC，转换为半角
 * @return string 返回转换后的字符串
 */
function convertStrType($str, $type) {

	$dbc = array(
		'０' , '１' , '２' , '３' , '４' ,
		'５' , '６' , '７' , '８' , '９' ,
		'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
		'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
		'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
		'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
		'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
		'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
		'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
		'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
		'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
		'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
		'ｙ' , 'ｚ' , '－' , '　' , '：' ,
		'．' , '，' , '／' , '％' , '＃' ,
		'！' , '＠' , '＆' , '（' , '）' ,
		'＜' , '＞' , '＂' , '＇' , '？' ,
		'［' , '］' , '｛' , '｝' , '＼' ,
		'｜' , '＋' , '＝' , '＿' , '＾' ,
		'￥' , '￣' , '｀'

	);

	$sbc = array( //半角
		'0', '1', '2', '3', '4',
		'5', '6', '7', '8', '9',
		'A', 'B', 'C', 'D', 'E',
		'F', 'G', 'H', 'I', 'J',
		'K', 'L', 'M', 'N', 'O',
		'P', 'Q', 'R', 'S', 'T',
		'U', 'V', 'W', 'X', 'Y',
		'Z', 'a', 'b', 'c', 'd',
		'e', 'f', 'g', 'h', 'i',
		'j', 'k', 'l', 'm', 'n',
		'o', 'p', 'q', 'r', 's',
		't', 'u', 'v', 'w', 'x',
		'y', 'z', '-', ' ', ':',
		'.', ',', '/', '%', ' #',
		'!', '@', '&', '(', ')',
		'<', '>', '"', '\'','?',
		'[', ']', '{', '}', '\\',
		'|', '+', '=', '_', '^',
		'￥','~', '`'

	);
	if($type == 'TODBC'){
		return str_replace( $sbc, $dbc, $str ); //半角到全角
	}elseif($type == 'TOSBC'){
		return str_replace( $dbc, $sbc, $str ); //全角到半角
	}else{
		return $str;
	}
}

/*
 * php递归删除指定目录下的的文件
 * @param string $dir 目录名
 * @return null
 */
//php删除指定目录下的的文件-用PHP怎么删除某目录下指定的一个文件？
function rmdirs($dir){
	//error_reporting(0);    函数会返回一个状态,我用error_reporting(0)屏蔽掉输出
	//rmdir函数会返回一个状态,我用@屏蔽掉输出
	$dir_arr = scandir($dir);
	foreach($dir_arr as $key=>$val){
		if($val == '.' || $val == '..'){}
		else {
			if(is_dir($dir.'/'.$val))
			{
				if(@rmdir($dir.'/'.$val) == 'true'){}    //去掉@您看看
				else
					rmdirs($dir.'/'.$val);
			}
			else
				unlink($dir.'/'.$val);
		}
	}
}