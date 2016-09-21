<?php
namespace Home\Controller;
use Think\Controller;
class PaperController extends Controller {
    public function index()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
			$this->display();
		}
    }

    public function PaperList()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
            $param = I('post.');
            deleteEmptyValue($param);
	    	$pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            if ($param['pub_start'] && $param['pub_end'])
                $param['publish_date'] = array(array('gt',$param['pub_start']),array('lt',$param['pub_end']));
            $string = '';
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $param['_string'] = $string;

	        $Paper = D('PaperView');
            unset($param['pub_start']);
            unset($param['pub_end']);
            unset($param['page']);
            unset($param['items']);
	        $result = $Paper->where($param)->page($pageNum,$itemsNum)->order('Paper.id')->select();
            $totalNum = $Paper->where($param)->count();
            $result[0]['totalNum'] = $totalNum;
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '论文列表';
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
            if ($query['pub_start'] && $query['pub_end']){
                $query['publish_date'] = array(array('gt',$query['pub_start']),array('lt',$query['pub_end']));
            }
            $string = '';
            if ($query['college_id']){
                $string .= $string ? ' AND ('.$query['college_id'].')' : '('.$query['college_id'].')';
                //unset($query['college_id']);
            }
            if ($string){
                $query['_string'] = $string;
            }


            $paper = D('PaperView');
            $query = objectToArray($query);
            unset($query['pub_start']);
            unset($query['pub_end']);
            unset($query['page']);
            unset($query['items']);
            if ($type == 'all'){
                $audit['descr'] = '导出所有。';
                $result = $paper->field($field)->where($query)->order('paper.id')->select();
            }
            else if ($type == 'current'){
                $audit['descr'] = '导出当前。';
                $result = $paper->field($field)->where($query)->page($pageNum,$itemsNum)->order('paper.id')->select();
            }
            else{
                return '未知错误';
            }

            $titleMap = array('person_name'=>'第一作者','col_name'=>'第一作者单位','name'=>'论文名','conference_name'=>'期刊/会议名称','publish_date'=>'发表时间','comment'=>'备注');
            $field = split(',', $field); 
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '论文信息';
            exportExcel($filename, $field, $result, $excelTitle);
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '论文信息';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] .= '导出论文信息';
            M('audit')->add($audit);
        }
    }
}