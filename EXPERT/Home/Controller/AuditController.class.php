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
            $this->ajaxReturn($result,'json');
	    }
    }
}