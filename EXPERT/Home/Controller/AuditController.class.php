<?php
namespace Home\Controller;
use Think\Controller;
class AuditController extends Controller {
    public function index()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
			$this->display();
		}
    }

    public function AuditList()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
            $param = I('post.');
            deleteEmptyValue($param);
	    	$pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数
	        $Audit = M('audit');
	        $result = $Audit->page($pageNum,$itemsNum)->order('Audit.id desc')->select();
            $totalNum = $Audit->count();
            $result[0]['totalNum'] = $totalNum;
			//操作记录日志
//			$audit['name'] = session('username');
//			$audit['ip'] = getIp();
//			$audit['module'] = '操作记录日志列表';
//			$audit['time'] = date('y-m-d h:i:s',time());
//			$audit['result'] = '成功';
//			$audit['descr'] = '查询所有字段';
//			M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
	    }
    }
}