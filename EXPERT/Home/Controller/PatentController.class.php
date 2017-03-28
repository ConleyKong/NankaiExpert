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

    public function patentList()
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

            $firstinventor_name=$param['firstinventor_name'];
            if($firstinventor_name)
                $query['firstinventor_name'] = array('like','%'.$firstinventor_name.'%');
            unset($param['firstinventor_name']);


            $name = $param['name'];
            if($name)
                $query['name']=array('like','%'.$name.'%');
            unset($param['name']);


            $owner_name = $param['owner_name'];
            if($owner_name)
                $query['owner_name']=array('like','%'.$owner_name.'%');
            unset($param['owner_name']);



            $string = '';
            if ($param['ownercollege_id']){
                $string .= $string ? ' AND ('.$param['ownercollege_id'].')' : '('.$param['ownercollege_id'].')';
                unset($param['ownercollege_id']);
            }
            if ($param['inventorcollege_id']){
                $string .= $string ? ' AND ('.$param['inventorcollege_id'].')' : '('.$param['inventorcollege_id'].')';
                unset($param['inventorcollege_id']);
            }
            if($param['keyword']){
                $keyword = $param['keyword'];
                $ts = " (patent.name like '%$keyword%' OR inventor.name like '%$keyword%')";
                $string .= $string?' AND '.$ts:$ts;
                unset($param['keyword']);
            }
            if ($string)
                $query['_string'] = $string;

            $query["valid"]=true;

	        $Patent = D('PatentView');

	        $result = $Patent->where($query)->page($pageNum,$itemsNum)->order('Patent.id')->select();
            $totalNum = $Patent->where($query)->count();
            $result[0]['totalNum'] = $totalNum;
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '专利列表';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['result'] = '成功';
            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
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
//            $condition['valid']=true;
            $patent = M('patent');
            $p = $patent->where($condition)->find();
            $p['valid']=false;
            $state = $patent->where($condition)->save($p);
            $name = $p["name"];
            //操作记录日志
            $audit['name'] = session('username');
            $audit['ip'] = getIp();
            $audit['module'] = '知识产权';
            $audit['time'] = date('y-m-d h:i:s',time());
            $audit['descr'] .= "删除专利: $id: $name ";


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

            $name = urldecode(I('get.name'));
            $owner_name = I('get.owner_name');

            $page = I('get.page');
            $items = I('get.items');

            $pageNum = $page? $page : 1;
            $itemsNum =  $items? $items : 10;

            $firstinventor_name = I('get.firstinventor_name');
            if($firstinventor_name)
                $query['firstinventor_name'] = array('like','%'.$firstinventor_name.'%');
            if($name)
                $query['name']=array('like','%'.$name.'%');
            if($owner_name)
                $query['owner_name']=array('like','%'.$owner_name.'%');

            $string = '';
            $ownercollege_id = I('get.ownercollege_id');
            if ($ownercollege_id){
                $string .= $string ? ' AND ('.$ownercollege_id.')' : '('.$ownercollege_id.')';
            }
            $inventorcollege_id = I('get.inventorcollege_id');
            if ($inventorcollege_id){
                $string .= $string ? ' AND ('.$inventorcollege_id.')' : '('.$inventorcollege_id.')';
            }
            $keyword = I('get.keyword');
            if($keyword){
                $ts = " (patent.name like '%$keyword%' OR inventor.name like '%$keyword%')";
                $string .= $string?' AND '.$ts:$ts;
            }
            if ($string)
                $query['_string'] = $string;

            $Patent = D('PatentView');

            $query['valid']=true;

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
            $titleMap = array('name'=>'专利名称','first_inventor'=>'第一发明人','owner_college_name'=>'所属单位','apply_no'=>'申请编号','apply_date'=>'申请日期','type'=>'专利类型','all_inventors'=>'参与人','grant_date'=>'授权日期','patent_state'=>'专利状态','comment'=>'备注');
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
                $file_name = "patent_info_".$str . "." . $file_type;

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
                $type_buffer = array();
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
                         */
                        $patent = M("patent");
                        $data = array();
                        $data["name"]= $v[0];//专利名称
                        $first_inventor = trim($v[1]);//第一发明人姓名
                        $data['first_inventor'] = $first_inventor;
                        $college_name = trim($v[2]);//学院
                        $data["apply_no"]=trim($v[3]);//申请编号
                        $data["apply_date"]=$v[4];//申请日期
                        $data["grant_date"]=$v[5];//授予日期
                        $type_name = trim($v[6]);//专利类型
                        $data["type"] = $type_name;
                        $all_inventors = trim($v[7]);//所有专利发明人，‘,’分割
                        $data['all_inventors']=$all_inventors;
                        $data['patent_state'] = trim($v[8]);//专利状态

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        //处理学院信息 college_id
                        $college_name = $college_name==''?'其他':$college_name;
                        $college_id = getForeignKeyFromDB($college_name,"college");
                        if($college_id>0){
                            $data["college_id"] = $college_id;
                        }else{
                            $error_counter++;
                            $errored_name[]=$data["name"]."（学院名".$college_name."有误）";
//                            continue;
                        }


                        // 处理第一发明人id
                            $first_inventor_id = getPidByNameAndCollege($first_inventor,$college_id);
                            if($first_inventor_id>0){
                                $data["first_inventor_id"] = $first_inventor_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（第一发明人".$first_inventor."不存在）";
//                                continue;
                            }

                        $condition["apply_no"] =$data["apply_no"];
                        $condition["name"]=$data["name"];
                        $isduplicated = $patent->where($condition)->find();
                        $patent->startTrans();

                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $patent->where($condition)->save($data);
                            if($num){
                                $patent->commit();
                                $result = $isduplicated['id'];
                                //将成功更新的数据记录到日志中
                                $update_counter++;
                                $updated_id[]=$result;
                                //删除所有patent_inventors相关表，稍后重新插入（以后来数据为准）
                                $flag["patent_id"]=(int)$result;
                                M('patent_inventors')->where($flag)->delete();
                            }
                        }else{
                            //插入数据
                            $result = $patent->add($data);
                            if($result){
                                $patent->commit();
                                //成功插入数据记录到日志中
                                $insert_counter++;
                                $inserted_id[]=$result;
                            }

                        }

                        if (empty($result))
                        {
                            $patent->rollback();
                            $error_counter++;
                            $errored_name[]=$data["name"]."写入失败！";
                        }else if($all_inventors!=''){
                            // 插入其他发明人职工号
                            $patent_id = (int)$result;
                            $inventors = explode(',',$all_inventors);//此处分隔符需要选用全角分号，因为Excel中输入的可能是用的中文输入法
                            foreach($inventors as $ivtor){
                                $person_id = getPidByNameAndCollege($ivtor,$college_id);

                                if($person_id>0){
                                    $token["patent_id"] = $patent_id ;
                                    $token["person_id"]  = $person_id  ;
                                    $existed = M('patent_inventors')->where($token)->find();
                                    if($existed==null){
                                        //插入patent_other_inventor数据
                                        $patent_other_inventor_result = M('patent_inventors')->add($token);
                                    }
                                }else{//可能获取不到这个外键的id
                                    $error_counter++;
                                    $errored_name[]=$data["name"]."（发明人 $ivtor 不存在）";
//                                    continue;
                                }
                            }
                        }
                    }

                }

                /*将操作结果写入日志*/
                $audit['descr'] = '从Excel文件中导入专利信息。';
                $audit['name'] = session('username');
                $audit['ip'] = getIp();
                $audit['module'] = '人员信息';
                $audit['time'] = date('y-m-d h:i:s', time());
                $error_counter>0?$audit['result'] ='警告':$audit['result'] = '成功';
                $descr = "\n 记录导入异常".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入异常的专利名： ";
                    foreach ($errored_name as $e_name){
                        $descr .= $e_name.",";
                    }
                }
                $descr .= "\n 成功插入记录".$insert_counter."条；\n";
                if($insert_counter>0){
                    //将插入成功的用户id记录下来
                    $descr .= "成功插入的专利id： ";
                    foreach ($inserted_id as $id){
                        $descr .= $id.",";
                    }
                }
                $descr .= "\n 成功更新记录".$update_counter."条；\n";
                if($update_counter>0){
                    //将更新成功的用户id记录下来
                    $descr .= "成功更新的专利id： ";
                    foreach ($updated_id as $id){
                        $descr .= $id.",";
                    }
                }
                $audit['descr'] .= $descr;
                M('audit')->add($audit);

                if($error_counter==0){
                    $this->success("导入工作成功（详情见日志）",'/Audit/index',2);
                }else{
                    $this->error("存在导入失败的记录，请查看日志进行修正！",'/Audit/index',2);
                }

            }

            $this->error("文件选择失败！");

        }
    }
}