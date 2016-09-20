<?php
namespace Home\Controller;
use Think\Controller;
class ProjectController extends Controller {

    public function index()
    {
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $this->display();
        }
    }

    public function ProjectList()
    {
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $param = I('post.');
            deleteEmptyValue($param);
            $pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
            $string='';
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $param['_string'] = $string;

            $Paper = D('ProjectView');
            unset($param['page']);
            unset($param['items']);
            $result = $Paper->where($param)->page($pageNum,$itemsNum)->order('project.id')->select();
            $totalNum = $Paper->where($param)->count();
            $result[0]['totalNum'] = $totalNum;
            $this->ajaxReturn($result,'json');
        }
    }


    public function getProjectTypeCount()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
    		$project = D('ProjectView');
			$type = $project->field(array('count(project.type_id) typecount','type_name'))->group('type_id')->select();
			$support = $project->field(array('count(support_no) supportcount','support_no'))->group('support_no')->select();
            $result = array();
            $result['type'] = $type;
            $result['support'] = $support;
            $this->ajaxReturn($result, 'json');
		}
    }

    public function getProjectTrend(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $project = M('project');
            
        }
    }
}