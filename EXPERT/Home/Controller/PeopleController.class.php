<?php
namespace Home\Controller;
use Org\Util\Date;
use Think\Controller;
class PeopleController extends Controller {
    public function index()
    {
    	if (! session('logged')){
    		$this->redirect('Index/index');
    	}
    	else{
			$this->display();
		}
    }

    public function personList()
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
                $param['birthday'] = array(array('gt',$param['startTime']),array('lt',$param['endTime']));
	        $string = '';
            if ($param['academichonor_id']){
                $string .= $string ? ' AND ('.$param['academichonor_id'].')' : '('.$param['academichonor_id'].')';
                unset($param['academichonor_id']);
            }
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $param['_string'] = $string;
            $person = D('PeopleView');
            unset($param['page']);
            unset($param['items']);
            unset($param['startTime']);
            unset($param['endTime']);
	        $result = $person->field('id,name,gender,employee_no,postdoctor,birthday,email,phone,first_class,second_class,college_name,academic_name')->where($param)->page($pageNum,$itemsNum)->order('person.id')->select();
            $totalNum = $person->where($param)->count();
            $result[0]['totalNum'] = $totalNum;

            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '人员列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] = '查询所有字段';
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


    public function delete()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            $id = $_GET['id'];
            $condition['id'] = $id;
            $people = M('person');
            $state = $people->where($condition)->delete();
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '人员列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr'] .= '删除人员id: $id ';


            if($state){
                $this->success('操作成功！');
                $audit['result'] = '成功';
                M('audit')->add($audit);
            }
            else{
                $audit['result'] = '失败';
                M('audit')->add($audit);
                $this->error('操作失败！');
            }
            ////////////////////////////////////////////////////////////////////////////////

//            $this->display();
//                $this->redirect('Event/index');

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
            $result = $person->field($field)->group($group)->select();
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
//            $person = D('PeopleView')->where("person.id=%d",$id)->select();
//            $award = D('AwardView')->where("award.first_id=%d",$id)->select();
//            $project = D('ProjectView')->where("project.owner_id=%d",$id)->select();
//            $paper = D('PaperView')->where("paper.firstauthor_id=%d",$id)->select();
//            $patent = D('PatentView')->where("patent.owner_id=%d",$id)->select();

            $person_condition['id']=$id;
            $person = D('PeopleView')->where($person_condition)->select();

            $award_condition['first_id']=$id;
            $award = D('AwardView')->where($award_condition)->select();

            $paper_condition['firstauthor_id']=$id;
            $paper = D('PaperView')->where($paper_condition)->select();

            $patent_condition['owner_id']=$id;
            $patent = D('PatentView')->where($patent_condition)->select();

            $project_condition['manager_id']=$id;
            $project = D('ProjectView')->where($project_condition)->select();


            $this->assign('person',$person);
            $this->assign('award',$award);
            $this->assign('paper',$paper);
            $this->assign('patent',$patent);
            $this->assign('project',$project);
            $this->display();
        }
    }
    
    
    /*
     * 文件的导入导出
     */
    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            //type:导出所有页或者导出当前页
            //query：查询条件
            //field:导出的字段
            $type = I('get.type');//$param['type'];
            $params = I('get.params');// $param['params'];
            $field = I('get.field');// $param['field'];
            deleteEmptyValue($params);
            $pageNum = $params['page'] ? $params['page'] : 1;
            $itemsNum =  $params['items'] ? $params['items'] : 10;

            if ($params['startTime'])
                $params['birthday'] = array('gt',$params['startTime']);
            $string = '';
            if ($params['academichonor_id']){
                $string .= $string ? ' AND ('.$params['academichonor_id'].')' : '('.$params['academichonor_id'].')';
//                unset($query['academichonor_id']);
            }
            if ($params['college_id']){
                $string .= $string ? ' AND ('.$params['college_id'].')' : '('.$params['college_id'].')';
//                unset($query['college_id']);
            }
            if ($string)
                $params['_string'] = $string;

            $person = D('PeopleView');
            $query = objectToArray($params);
            unset($query['page']);
            unset($query['items']);
            unset($query['startTime']);
            unset($query['endTime']);
            if ($type == 'all'){
                $audit['descr'] = '导出所有。';
                $result = $person->field($field)->where($query)->order('person.id')->select();
            }else if ($type == 'current'){
                $audit['descr'] = '导出当前。';
                $result = $person->field($field)->where($query)->page($pageNum,$itemsNum)->order('person.id')->select();
            }else{
                return '未知错误';
            }

            $titleMap = array('name'=>'姓名','gender'=>'性别','employee_no'=>'职工号','college_name'=>'学院/部门','postdoctor'=>'博士后','academic_name'=>'荣誉称号','birthday'=>'出生日期','email'=>'邮箱','phone'=>'电话','first_class'=>'一级学科','second_class'=>'二级学科');
            $field = split(',', $field);
            $excelTitle = array();
            foreach ($field as $value) {
                array_push($excelTitle, $titleMap[$value]);
            }
            $filename = '人员信息';
            exportExcel($filename, $field, $result, $excelTitle);
            //审计日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '人员列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] .= '导出人员列表';
            M('audit')->add($audit);

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
                if (!in_array($extension,$excel_type))
                {
                    $this->error ( '不是Excel文件，请重新选择文件！' );
                }
                /*设置上传路径*/
                $savePath = './Public/upfile/Excel/';
                /*以时间来命名上传的文件*/
                $str = date ( 'Ymdhis' );
                $file_name = "people_info_".$str . "." . $file_type;

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
                $degree_buffer = array();
                $mentor_buffer = array();
                $type_buffer = array();
                $title_buffer = array();
                $college_buffer = array();
                $honor_buffer = array();

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
                         * 员工号(employee_no)
                         * 姓名(name)
                         * 性别(gender)
                         * 出生日期(birthday)
                         * 一级学科(first_class)
                         *
                         * 二级学科(second_class)
                         * 手机号(phone)
                         * 办公电话(office_phone)
                         * 邮箱地址(email)
                         * 学位(degree_id)
                         *
                         * 导师类型(mentor_type_id)
                         * 人员类型(type_id)
                         * 职称(title_id)
                         * 所在学院(college_id)
                         * 是否是博士后(postdoctor)
                         *
                         * 学术荣誉(person_honor表中有honor_id和person_id)
                         */
                        $people = M("person");
                        $data = array();
                        $data["employee_no"]= $v[0];
                        $data["name"]= $v[1];
                        $data["gender"]=$v[2];
                        $data["birthday"]=$v[3];
                        $data["first_class"]=$v[4];
                        $data["second_class"]=$v[5];
                        $data["phone"]=$v[6];
                        $data["office_phone"]=$v[7];
                        $data["email"]=$v[8];

                        $degree_name = trim($v[9]);
                        $mentor_type_name = trim($v[10]);
                        $type_name = trim($v[11]);
                        $title_name = trim($v[12]);
                        $college_name = trim($v[13]);
                        $postdoctor = trim($v[14]);
                        $person_honor_names=trim($v[15]);

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理学历信息 person_degree
                            $degree_id = getForeignKey($degree_name,"person_degree",$degree_buffer);
                            if($degree_id>0){
                                $data["degree_id"] = $degree_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（用户学历信息有误）";
                                continue;
                            }


                        //处理导师类型信息 person_mentor_type
                            $mentor_type_id = getForeignKey($mentor_type_name,"person_mentor_type",$mentor_buffer);
                            if($mentor_type_id>0){
                                $data["mentor_type_id"] = $mentor_type_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（用户导师类型信息有误）";
                                continue;
                            }

                        //处理人员类型信息 person_type
                            $type_id = getForeignKey($type_name,"person_type",$type_buffer);
                            if($type_id>0){
                                $data["type_id"] = $type_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（用户人员类型有误）";
                                continue;
                            }


                        //处理人员职称信息 person_title
                            $title_id = getForeignKey($title_name,"person_title",$title_buffer);
                            if($type_id>0){
                                $data["title_id"] = $title_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（职称信息有误）";
                                continue;
                            }


                        //处理学院信息 college_id
                            $college_id = getForeignKey($college_name,"college",$college_buffer);
                            if($college_id>0){
                                $data["college_id"] = $college_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（学院信息有误）";
                                continue;
                            }


                        /*
                         * 处理博士后信息
                         */
                        $ispostdoctor=false;
                        //转化输入的y/n,是/否为true和false
                        if($postdoctor=='Y'||$postdoctor=='y'||$postdoctor=='是'){
                            $ispostdoctor=true;
                        }
                        $data["postdoctor"] = $ispostdoctor;

                        $condition["employee_no"] =$data["employee_no"];
                        $condition["name"]=$data["name"];
                        $isduplicated = $people->where($condition)->find();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $people->where($condition)->save($data);
                            $result = $isduplicated['id'];
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$result;

                            //删除所有person_honor相关表，稍后重新插入（以后来数据为准）
                            $flag["person_id"]=(int)$result;
                            M('person_honor')->where($flag)->delete();

                        }else{
                            //插入数据
                            $result = $people->add($data);
                            //成功插入数据记录到日志中
                            $insert_counter++;
                            $inserted_id[]=$result;
                        }


                        if (empty($result))
                        {
                            $error_counter++;
                            $errored_name[]=$data["name"];
                        }else if($person_honor_names!=''){
                            /* honor的插入需要特别注意，由于是一对多关系，因此会涉及三张表：
                             * 插入的主表为person_honor  涉及到 person中的id   和  academic_honor中的id
                             * $u_honor = $v[15];
                             */
//                            dump($data);//显示添加的数据
                            $person_id = (int)$result;
                            $honor_list = explode('；',$person_honor_names);//此处分隔符需要选用全角分号，因为Excel中输入的可能是用的中文输入法
                            foreach($honor_list as $honor_name){
                                $honor_id = getForeignKey($honor_name,'academic_honor',$honor_buffer);
                                //可能获取不到这个外键的id
                                if($honor_id>0){
                                    $token["person_id"] = $person_id ;
                                    $token["honor_id"]  = $honor_id  ;
                                    $existed = M('person_honor')->where($token)->find();
                                    if($existed==null){
                                        //插入honor数据
                                        $honor_result = M('person_honor')->add($token);
                                    }
                                }else{
                                    $error_counter++;
                                    $errored_name[]=$data["name"]."（学术称号 $honor_name 有误）";
                                    continue;
                                }
                            }
                        }

                    }

                }

                /*将操作结果写入日志*/
                $audit['descr'] = '从Excel文件中导入人员信息。';
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '人员信息';
                $audit['time'] = date('y-m-d h:i:s', time());
                $error_counter>0?$audit['result'] ='警告':$audit['result'] = '成功';
                $descr = "\n 记录导入失败".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入失败的用户名： ";
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
//                    $this->success("恭喜您，成功导入或更新数据"+($insert_counter+$update_counter)+"条！（详情见日志）",'',5);
                    $this->success("导入工作成功（详情见日志）",'',3);
                }else{
                    $this->error("存在导入失败的记录，请查看日志进行修正！",'',30);
                }

            }

            $this->error("文件选择失败！");

        }
    }
    
    
}