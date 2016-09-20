<?php
namespace Home\Controller;
use Think\Controller;
class AwardController extends Controller {
    public function index()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
			$this->display();
		}
    }

    public function awardList()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
            $param = I('post.');
            deleteEmptyValue($param);
	    	$pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            if ($param['startTime'])
                $param['time'] = array(array('gt',$param['startTime']),array('lt',$param['endTime']));
            if ($param['person_startTime'])
                $param['birthday'] = array(array('gt',$param['person_startTime']),array('lt',$param['person_endTime']));
            $string = '';
            if ($param['level']){
                $string .= $string ? ' AND ('.$param['level'].')' : '('.$param['level'].')';
                unset($param['level']);
            }
            if ($param['academichonor_id']){
                $string .= $string ? ' AND ('.$param['academichonor_id'].')' : '('.$param['academichonor_id'].')';
                unset($param['academichonor_id']);
            }
            if ($param['grade_id']){
                $string .= $string ? ' AND ('.$param['grade_id'].')' : '('.$param['grade_id'].')';
                unset($param['grade_id']);
            }
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $param['_string'] = $string;

	        $award = D('AwardView');
            unset($param['page']);
            unset($param['items']);
            unset($param['startTime']);
            unset($param['endTime']);
            unset($param['person_startTime']);
            unset($param['person_endTime']);
	        $result = $award->field('id,name,level,time,comment,birthday,person_name,grade_name,academichonor_name,col_name')->where($param)->page($pageNum,$itemsNum)->order('award.id')->select();
            $totalNum = $award->where($param)->count();
            $result[0]['totalNum'] = $totalNum;
            $this->ajaxReturn($result,'json');
	    }
    }


    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $type = I('get.type');//$param['type'];
            $query = I('get.query');// $param['query'];
            $field = I('get.field');// $param['field'];
            deleteEmptyValue($query);
            $pageNum = $query['page'] ? $query['page'] : 1;
            $itemsNum =  $query['items'] ? $query['items'] : 10;
            if ($query['startTime'] && $query['endTime'])
                $query['time'] = array(array('gt',$query['startTime']),array('lt',$query['endTime']));
            if ($query['person_startTime'] && $query['person_endTime'])
                $query['birthday'] = array(array('gt',$query['person_startTime']),array('lt',$query['person_endTime']));
            $string = '';
            if ($query['level']){
                $string .= $string ? ' AND ('.$query['level'].')' : '('.$query['level'].')';
                //unset($query['level']);
            }
            if ($query['academichonor_id']){
                $string .= $string ? ' AND ('.$query['academichonor_id'].')' : '('.$query['academichonor_id'].')';
                //unset($query['academichonor_id']);
            }
            if ($query['grade_id']){
                $string .= $string ? ' AND ('.$query['grade_id'].')' : '('.$query['grade_id'].')';
                //unset($query['grade_id']);
            }
            if ($query['college_id']){
                $string .= $string ? ' AND ('.$query['college_id'].')' : '('.$query['college_id'].')';
                unset($query['college_id']);
            }
            if ($string)
                $query['_string'] = $string;

            $award = D('AwardView');
            $query = objectToArray($query);
            unset($query['page']);
            unset($query['items']);
            unset($query['startTime']);
            unset($query['endTime']);
            unset($query['person_startTime']);
            unset($query['person_endTime']);
            if ($type == 'all'){
                $result = $award->field($field)->where($query)->order('award.id')->select();
            }
            else if ($type == 'current'){
                $result = $award->field($field)->where($query)->page($pageNum,$itemsNum)->order('award.id')->select();
            }
            else
                return '未知错误';
            $titleMap = array('name'=>'奖励名称','level'=>'奖励级别','time'=>'获奖时间','comment'=>'备注','person_name'=>'姓名','birthday'=>'出生日期','grade_name'=>'职称','academichonor_name'=>'学术荣誉','col_name'=>'学院');
            $field = split(',', $field); 
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '获奖信息';
            exportExcel($filename, $excelTitle, $result);
        }
    }

    //获取人员职称
    public function getGradeList(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $grade = M('person_title');
            $result = $grade->field('id,name')->select();
            $this->ajaxReturn($result, 'json');
        }
    } 

    //获取荣誉称号
    public function getAcademichonorList(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $academichonor = M('academic_honor');
            $result = $academichonor->field('id,name')->select();
            $this->ajaxReturn($result, 'json');
        }
    } 
}