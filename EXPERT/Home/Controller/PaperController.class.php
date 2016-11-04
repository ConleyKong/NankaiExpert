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

            $pub_start = $param['pub_start'];
            $pub_end = $param['pub_end'];
            $publish_date = array();
            if ($pub_start)
                $publish_date = array('gt',$pub_start);
            if($pub_end)
                $publish_date = array('lt',$pub_end);
            if($publish_date)
                $query['publish_date'] = $publish_date;


            if ($param['name'])
                $query['name']=array('like','%'.$param['name'].'%');
            if ($param['conference_name'])
                $query['conference_name']=array('like','%'.$param['conference_name'].'%');
            if ($param['person_name'])
                $query['person_name']=array('like','%'.$param['person_name'].'%');

            $string = '';
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if ($string)
                $query['_string'] = $string;

	        $paper = D('PaperView');
            unset($param['pub_start']);
            unset($param['pub_end']);
            unset($param['page']);
            unset($param['items']);

            $query["valid"]=true;

	        $result = $paper->where($query)->page($pageNum,$itemsNum)->order('paper.id')->select();
            $totalNum = $paper->where($query)->count();
            $result[0]['totalNum'] = $totalNum;
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '论文列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
	    }
    }


    /*
    * 删除指定paper
    * 将paper的valid置为false
    */
    public function delete()
    {

        if (!session('logged')){
            $this->redirect('Index/index');
        }
        else {
            $id = $_GET['id'];
            $condition['id'] = $id;
//            $condition['valid']=true;
            $paper = M('paper');
            $p = $paper->where($condition)->find();
            $p['valid']=false;
            $state = $paper->where($condition)->save($p);
            $name = $p["name"];
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '成果列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr'] .= "删除成果: $id: $name ";


            if($state>0){
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


    /*
     * 导入导出功能
     */

    public function export(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{

            $type = I('get.type');//$param['type'];
            $field = I('get.field');// $param['field'];

            $page = I('get.page');
            $items = I('get.items');

            $pageNum = $page? $page : 1;
            $itemsNum =  $items? $items : 10;


            $pub_start = I('get.pub_start');
            $pub_end = I('get.pub_end');
            $publish_date = array();
            if ($pub_start)
                $publish_date = array('gt',$pub_start);
            if($pub_end)
                $publish_date = array('lt',$pub_end);
            if($publish_date)
                $query['publish_date'] = $publish_date;


            $conference_name = I('get.conference_name');
            $person_name = I('get.person_name');
            if ($person_name)
                $query['person_name'] = array('like','%'.$person_name.'%');
            if ($conference_name)
                $query['conference_name'] = array('like','%'.$conference_name.'%');



            $college_id = I('get.college_id');
            $string = '';
            if ($college_id)
                $string .= $string ? ' AND ('.$college_id.')' : '('.$college_id.')';

            if ($string)
                $query['_string'] = $string;

            $query['valid']=true;

            $paper = D('PaperView');

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
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '论文信息';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            $audit['descr'] .= '导出论文信息';
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
                //导入paper表
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
                $file_name = "paper_info_".$str . "." . $file_type;

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

                /*存储临时数组，缓存少部分数据
                *  缓存数据类型主要看外键有哪些，外键的表是不是够小，若表太大的话不建议缓存
                */
                $college_buffer = array();

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
                         *
                         * 		，
                         *  需要特殊处理的字段：第一作者id，学院id，联系作者id
                         */
                        $paper = M("paper");
                        $data = array();
                        $data["name"]= $v[0];//论文名（name）
                        $first_author_no = trim($v[1]);//第一作者职工号(first_author_id->person(employee_num))
                        $data["publish_date"]=$v[2];//发表日期(publish_date)
                        $contact_author_no = $v[3];//联系作者id//联系作者职工号(contact_author_id->person(employee_num))
                        $data["paper_type"]=$v[4];//论文类型(paper_type)
                        $data["conference_name"]=$v[5];//期刊/会议名称(conference_name)
                        $data["other_author"]=$v[6];//其他作者（学院），多个作者中间用分号隔开(other_author)
                        $college_name = $v[7];//学院名(college_id->college(name))

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理第一作者信息 person
                        $first_author_id = getPersonIdByEmployeeNo($first_author_no);
                        if($first_author_id>0){
                            $data["first_author_id"] = $first_author_id;
                        }else{
                            $error_counter++;
                            $errored_name[]=$data["name"]."（第一作者职工号不存在）";
                            continue;
                        }

                        //处理联系作者信息 联系作者职工号(contact_author_id->person(employee_num))

                        $contact_author_id = getPersonIdByEmployeeNo($contact_author_no);
                        if($contact_author_id>0){
                            $data["contact_author_id"] = $contact_author_id;
                        }else{
                            $error_counter++;
                            $errored_name[]=$data["name"]."（联系作者职工号不存在）";
                            continue;
                        }

                        //处理学院id college
                        $college_id = getForeignKey($college_name,"college",$college_buffer);
                        if($college_id>0){
                            $data["college_id"] = $college_id;
                        }else{
                            $error_counter++;
                            $errored_name[]=$data["name"]."（学院名不存在）";
                            continue;
                        }

                        $condition = array("name"=>$v[0]);
                        $isduplicated = $paper->where($condition)->find();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $paper->where($condition)->save($data);
                            $result = $isduplicated['id'];
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$result;
                        }else{
                            //插入数据
                            $result = $paper->add($data);
                            //成功插入数据记录到日志中
                            $insert_counter++;
                            $inserted_id[]=$result;
                        }
                        if (empty($result))
                        {
//                            $this->error ( '数据插入/更新失败！' );
                            $error_counter++;
                            $errored_name[]=$data["name"];
                        }

                    }

                }

                /*将操作结果写入日志*/
                $audit['descr'] = '从Excel文件中导入论文信息。';
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '科研成果';
                $audit['time'] = date('y-m-d h:i:s', time());
                $error_counter>0?$audit['result'] ='警告':$audit['result'] = '成功';
                $descr = "\n 记录导入失败".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入失败的论文名： ";
                    foreach ($errored_name as $e_name){
                        $descr .= $e_name.",";
                    }
                }
                $descr .= "\n 成功插入记录".$insert_counter."条；\n";
                if($insert_counter>0){
                    //将插入成功的用户id记录下来
                    $descr .= "成功插入的论文id： ";
                    foreach ($inserted_id as $id){
                        $descr .= $id.",";
                    }
                }
                $descr .= "\n 成功更新记录".$update_counter."条；\n";
                if($update_counter>0){
                    //将更新成功的用户id记录下来
                    $descr .= "成功更新的论文id： ";
                    foreach ($updated_id as $id){
                        $descr .= $id.",";
                    }
                }
                $audit['descr'] .= $descr;
                M('audit')->add($audit);

                if($error_counter==0){
                    $this->success("恭喜您，科研成果数据导入操作成功！（详情见操作记录模块）",'',4);
                }else{
                    $this->error("存在导入失败的记录，请查看操作记录模块的日志进行修正！",'',4);
                }

            }

            $this->error("文件选择失败！");

        }
    }

}