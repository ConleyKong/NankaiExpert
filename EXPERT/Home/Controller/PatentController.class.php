<?php
namespace Home\Controller;
use Think\Controller;
class PatentController extends Controller {
    public function index()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
			$this->display();
		}
    }

    public function PatentList()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
            $param = I('post.');
            deleteEmptyValue($param);
	    	$pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            $string = '';
            if ($param['ownercollege_id']){
                $string .= $string ? ' AND ('.$param['ownercollege_id'].')' : '('.$param['ownercollege_id'].')';
                unset($param['ownercollege_id']);
            }
            if ($param['inventorcollege_id']){
                $string .= $string ? ' AND ('.$param['inventorcollege_id'].')' : '('.$param['inventorcollege_id'].')';
                unset($param['inventorcollege_id']);
            }
            if ($string)
                $param['_string'] = $string;

	        $Patent = D('PatentView');
            unset($param['page']);
            unset($param['items']);
	        $result = $Patent->where($param)->page($pageNum,$itemsNum)->order('Patent.id')->select();
            $totalNum = $Patent->where($param)->count();
            $result[0]['totalNum'] = $totalNum;
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '专利列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
	    }
    }


    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $type = I('get.type');
            $query = I('get.query');
            $field = I('get.field');
            deleteEmptyValue($query);
            $pageNum = $query['page'] ? $query['page'] : 1;
            $itemsNum =  $query['items'] ? $query['items'] : 10;
            if ($query['pub_start'] && $query['pub_end'])
                $query['publish_date'] = array(array('gt',$query['pub_start']),array('lt',$query['pub_end']));
            if ($query['college_id']){
                $string .= $string ? ' AND ('.$query['college_id'].')' : '('.$query['college_id'].')';
                //unset($query['college_id']);
            }
            if ($string)
                $query['_string'] = $string;

            $Patent = D('PatentView');
            $query = objectToArray($query);
            unset($query['pub_start']);
            unset($query['pub_end']);
            unset($query['page']);
            unset($query['items']);
            if ($type == 'all'){
                $audit['descr'] = '导出所有。';
                $result = $Patent->field($field)->where($query)->order('Patent.id')->select();
            }
            else if ($type == 'current'){
                $audit['descr'] = '导出单页。';
                $result = $Patent->field($field)->where($query)->page($pageNum,$itemsNum)->order('Patent.id')->select();
            }
            else
                return '未知错误';
            $titleMap = array('name'=>'专利名称','owner_name'=>'专利权人/申请人','ownercollege_name'=>'所在单位','firstinventor_name'=>'第一发明人','inventorcollege_name'=>'第一发明人单位','type'=>'专利类型','apply_no'=>'专利（申请）号','apply_date'=>'专利申请日','grant_date'=>'专利授权日','comment'=>'备注');
            $field = split(',', $field); 
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '专利信息';
            exportExcel($filename, $field, $result, $excelTitle);
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '专利列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] = '导出专利列表信息';
            M('audit')->add($audit);
        }
    }
}