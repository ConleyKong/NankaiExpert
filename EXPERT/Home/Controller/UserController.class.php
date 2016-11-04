<?php
namespace Home\Controller;
use Org\Util\Date;
use Think\Controller;
class UserController extends Controller {
    public function index()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
			$this->display();
		}
    }

    public function getUserList()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{

            $param = I('post.');
            deleteEmptyValue($param);
            $pageNum = $param['page'] ? $param['page'] : 1;  //当前页
            $itemsNum =  $param['items'] ? $param['items'] : 10; //每页个数

            unset($param['page']);
            unset($param['items']);

            $string = '';
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $param['_string'] = $string;

            $query = $param;
            $query['valid']=1;
            $query['account']=array('neq',session('username'));

            $user = D('UserView');
	        $result = $user
                ->where($query)->page($pageNum,$itemsNum)->order('user.id')->select();
            $totalNum = $user->where($query)->count();
            $result[0]['totalNum'] = $totalNum;


            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '用户列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] = '显示当前用户列表';
            M('audit')->add($audit);

            $this->ajaxReturn($result,'json');
	    }
    }

    public function update()
    {
        if ( ! session('logged'))
        {
            $this->redirect('Index/index');
        }
        $id = I('post.id',0,'int');
        $data['name'] = I('post.name','暂无','string');
        $data['gender'] = I('post.gender','暂无','string');
        $data['employee_no'] = I('post.employee_no','暂无','string');
        $data['col_id'] = intval(I('post.col_id'));
        $data['postdoctor'] = I('post.postdoctor',0,'int');
        $data['birthday'] = I('post.birthday','暂无','string');
        $data['email'] = I('post.email','暂无','string');
        $data['phone'] = I('post.phone','暂无','string');
        $data['first_class'] = I('post.first_class','暂无','string');
        $data['second_class'] = I('post.second_class','暂无','string');
        $result  = M('person')->where("id=%d",$id)->save($data);
        echo $result ? '1' : '0';
    }

    public function add()
    {
        if ( ! session('logged'))
        {
            $this->redirect('Index/index');
        }

        $data['name'] = I('post.name','暂无','string');
        $data['gender'] = I('post.gender','暂无','string');
        $data['employee_no'] = I('post.employee_no','暂无','string');
        $data['postdoctor'] = I('post.postdoctor',0,'int');
        $data['birthday'] = I('post.birthday','暂无','string');
        $data['email'] = I('post.email','暂无','email');
        $data['phone'] = I('post.phone','暂无','string');
        $data['first_class'] = I('post.first_class','暂无','string');
        $data['second_class'] = I('post.second_class','暂无','string');

        $person = M('person');
        $result  = $person->add($data);
        if ($result)
        {
            $this->success('新增成功', 'index');
        }
        else
        {
            $this->error('数据插入失败'.$person->getError());
        }
    }


    public function del()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {

            $id = $_GET['id'];
            $condition['id'] = $id;

            $user = M('user');
            $u = $user->where($condition)->find();
            $u['valid']=0;
            $state = $user->where($condition)->save($u);
            $name = $u["name"];
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '人员列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr'] .= "删除人员: $id: $name ";

            if($state>0){
                $audit['result'] = '成功';
                M('audit')->add($audit);
                $this->success('操作成功！');
            }else{
                $audit['result'] = '失败';
                M('audit')->add($audit);
                $this->error('操作失败！');
            }

        }
    }
    
    public function set_status(){
        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            $id = $_GET['id'];
            $flag = $_GET['flag'];
            $condition['id'] = $id;

            $user = M('user');
            $u = $user->where($condition)->find();
            $u['status_id'] = (int)$flag;

            $state = $user->where($condition)->save($u);
            $name = $u["name"];
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '人员列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr'] .= "人员状态切换: $id: $name : $flag ";

            if($state>0){
                $audit['result'] = '成功';
                M('audit')->add($audit);
                $this->success('操作成功！');
            }else{
                $audit['result'] = '失败';
                M('audit')->add($audit);
                $this->error('操作失败！');
//                dump($u);
            }

        }
    }

    //获取学院列表
    function getCollegeList(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $result = M('college')->field('id,name')->select();
            $this->ajaxReturn($result, 'json');
        }
    }

    function query(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $this->display();
        }
    }

    function queryAPI(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $field = I('post.field', null, 'string');
            $group = I('post.group', null, 'string');
            $person = D('PeopleView');
            $condition['valid']=true;
            $result = $person->where($condition)->field($field)->group($group)->select();
            $this->result = $result;
            $this->ajaxReturn($result, 'json');
        }
    }

    function detail(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $id = I('get.id', null, 'int');


            $person_condition['id']=$id;
            $person_condition['valid']=true;
            $person = D('PeopleView')->where($person_condition)->select();

            $award_condition['first_id']=$id;
            $award_condition['valid']=true;
            $award = D('AwardView')->where($award_condition)->select();

            $paper_condition['firstauthor_id']=$id;
            $paper_condition['valid']=true;
            $paper = D('PaperView')->where($paper_condition)->select();

            $patent_condition['owner_id']=$id;
            $patent_condition['valid']=true;
            $patent = D('PatentView')->where($patent_condition)->select();

            $project_condition['manager_id']=$id;
            $project_condition['valid']=true;
            $project = D('ProjectView')->where($project_condition)->select();


            $this->assign('person',$person);
            $this->assign('award',$award);
            $this->assign('paper',$paper);
            $this->assign('patent',$patent);
            $this->assign('project',$project);
            $this->display();
        }
    }
    
    

    
    
}