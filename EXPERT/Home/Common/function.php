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
 * $i_buffer=null，外表数据缓存
 * 返回值：
 * 获得的外表的id
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
