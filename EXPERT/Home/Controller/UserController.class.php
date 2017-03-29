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

    public function import(){
        if (!session('logged')) {
            $this->redirect('Index/index');
        } else {
            //如果文件非空
            if (! empty ( $_FILES ['file_import'] ['name'] ))
            {
                //导入person表
                $tmp_file = $_FILES ['file_import'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['file_import'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                /*判别是不是.xls文件，判别是不是excel文件*/
                $excel_type = array("xls","xlsx");
                $extension = strtolower ( $file_type );
                if (!in_array($extension,$excel_type)){
                    $this->error ( '不是Excel文件，请重新选择文件！' );
                }
                /*设置上传路径*/
                $savePath = './Public/upfile/Excel/';
                /*以时间来命名上传的文件*/
                $str = date ( 'Ymdhis' );
                $file_name = "lab_info_".$str . "." . $file_type;

                /*是否上传成功*/
                if (! copy ( $tmp_file, $savePath . $file_name ))
                {
                    $this->error ( "服务器错误，上传失败！（error in peoplecontroller） " );
                }
                /*
                   *对上传的Excel数据进行处理生成编程数据,这个函数会在下面第三步的ExcelToArray类中
                  注意：这里调用执行了第三步类里面的read函数，把Excel转化为数组并返回给$res,再进行数据库写入
                */
                $res = readExcel( $savePath . $file_name ,$extension);
                /*
                     重要代码 解决Thinkphp M、D方法不能调用的问题
                     如果在thinkphp中遇到M 、D方法失效时就加入下面一句代码
                 */
                //spl_autoload_register ( array ('Think', 'autoload' ) );

                /*存储临时数组，缓存少部分数据*/
                $college_buffer = array();

                //默认的学院id为其他：23
                $default_college_id = 23;

                //统计导入的结果
                $insert_counter=0;//成功插入的新数据数
                $inserted_id = array();//成功插入的记录的id
                $update_counter=0;//更新的数据数
                $updated_id=array();//记录成功更新的数据的id
                $error_counter=0;//插入失败的数据数
                $errored_name=array();//记录插入失败的专家的姓名

                /*对生成的数组进行数据库的写入*/
                foreach ( $res as $k => $v )
                {
                    if ($k != 0)
                    {
                        /*
                         *输入数据表的表结构：
                         */
                        $user = M("user");
                        $data = array();
                        $real_name = trim($v[0]);
                        $data["account"] = $real_name;//用户名
                        $data["real_name"]= $real_name;//用户名
                        $college_name= trim($v[1]);//所属学院
                        $roles=trim($v[2])==""?"普通用户":trim($v[2]);//角色（普通用户或者管理员）
                        $password = trim($v[3])==""?"nkukjc":trim($v[3]);//密码
                        $status = trim($v[4])==""?"待审核":trim($v[4]);//用户状态默认

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        //处理学院信息 college_id
                        $college_name = $college_name==''?'其他':$college_name;
                        $college_id = getForeignKeyFromDB($college_name,"college");
                        if($college_id>0){
                            $data["college_id"] = $college_id;
                        }else{
                            $error_counter++;
                            $errored_name[]=$data["name"]."（学院 $college_name 不存在）";
                            continue;
                        }
                        $role_id = getForeignKeyFromDB($roles,"role_type");
                        if($role_id>0){
                            $data['role_id'] = $role_id;
                        }else{
                            $error_counter++;
                            $errored_name[]=$data["name"]."（用户角色 $roles 不存在）";
                            continue;
                        }
                        $status_id = getForeignKeyFromDB($status,"user_status");
                        if($status_id>0){
                            $data['status_id'] = $status_id;
                        }else{
                            $error_counter++;
                            $errored_name[]=$data["name"]."（用户状态 $status 不存在）";
                            continue;
                        }

                        $condition["real_name"]=$data["real_name"];
                        $condition["college_id"]=$college_id;
                        $isduplicated = $user->where($condition)->find();

                        if((int)$isduplicated['id']>0){//数据库中存在该用户，停止插入
                            $error_counter++;
                            $errored_name[]=$data["name"]."（账户 $real_name 已存在）";
                        }else{
                            //插入数据
                            $user->startTrans();
                            $data["password"]=md5($password);
                            $data['reg_date'] = date('y-m-d h:i:s', time());
                            $data['valid'] = 1;//新用户有效
                            $result = $user->add($data);
                            if($result){
                                $user->commit();
                                //成功插入数据记录到日志中
                                $insert_counter++;
                                $inserted_id[]=$result;
                            }

                        }

                        if (empty($result))
                        {
                            $user->rollback();
                            $error_counter++;
                            $errored_name[]=$data["name"];
                        }
                    }

                }

                /*将操作结果写入日志*/
                $audit['descr'] = '从Excel文件中导入用户信息。';
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '系统用户';
                $audit['time'] = date('y-m-d h:i:s', time());
                $error_counter>0?$audit['result'] ='警告':$audit['result'] = '成功';
                $descr = "\n 记录导入异常".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入异常的用户名： ";
                    foreach ($errored_name as $e_name){
                        $descr .= $e_name.",";
                    }
                }
                $descr .= "\n 成功插入记录".$insert_counter."条；\n";
                if($insert_counter>0){
                    //将插入成功的用户id记录下来
                    $descr .= "成功插入的用户id： ";
                    foreach ($inserted_id as $id){
                        $descr .= $id.",";
                    }
                }
                $descr .= "\n 成功更新记录".$update_counter."条；\n";
                if($update_counter>0){
                    //将更新成功的用户id记录下来
                    $descr .= "成功更新的用户id： ";
                    foreach ($updated_id as $id){
                        $descr .= $id.",";
                    }
                }
                $audit['descr'] .= $descr;
                M('audit')->add($audit);

                if($error_counter==0){
                    $this->success("导入工作成功（详情见日志）",'/Audit/index',2);
                }else{
                    $this->error("存在导入异常的记录，请查看日志进行修正！",'/Audit/index',2);
                }

            }

            $this->error("文件选择失败！");

        }
    }

    
    
}