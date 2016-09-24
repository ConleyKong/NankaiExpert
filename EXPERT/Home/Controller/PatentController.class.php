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

            $param["valid"]=true;
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
            //审计日志
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
                        $first_inventor_no = trim($v[1]);//第一发明人职工号
                        $college_name = trim($v[2]);//学院
                        $data["apply_no"]=$v[3];//申请编号
                        $data["apply_date"]=$v[4];//申请日期
                        $data["grant_date"]=$v[5];//授予日期
                        $type_name = trim($v[6]);//专利类型
                        $data["type"] = $type_name;
                        $others_nos = trim($v[7]);//其他发明人职工号（使用；分割）
                        $extra_names = trim($v[8]);//校外发明人姓名（使用；分割）

                        $owner_no = trim($v[9]);//专利所有人职工号

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理//第一发明人职工号
                            $first_inventor_id = getPersonIdByEmployeeNo($first_inventor_no);
                            if($first_inventor_id>0){
                                $data["first_inventor_id"] = $first_inventor_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（第一发明人职工号不存在）";
                                continue;
                            }

                        //处理学院信息 college_id
                            $college_id = getForeignKey($college_name,"college",$college_buffer);
                            if($college_id>0){
                                $data["college_id"] = $college_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（学院名有误）";
                                continue;
                            }


                        //处理专利所有人职工号
                            $owner_id = getPersonIdByEmployeeNo($owner_no);
                            if($owner_id>0){
                                $data["owner_id"] = $owner_id;
                            }else{
                                $error_counter++;
                                $errored_name[]=$data["name"]."（专利所有人职工号不存在）";
                                continue;
                            }

//                        //处理专利类型信息 patent_type
//                        if($type_name!=''){
//                            $type_id = getForeignKey($type_name,"person_type",$type_buffer);
//                            if($type_id>0){
//                                $data["type_id"] = $type_id;
//                            }else{
//                                $error_counter++;
//                                $errored_name[]=$data["name"]."（用户人员类型有误）";
//                                continue;
//                            }
//                        }


                        $condition["apply_no"] =$data["apply_no"];
                        $condition["name"]=$data["name"];
                        $isduplicated = $patent->where($condition)->find();
                        if((int)$isduplicated['id']>0){//数据库中存在相同数据，使用更新操作
                            $num = $patent->where($condition)->save($data);
                            $result = $isduplicated['id'];
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$result;

                            //删除所有patent_other(extra)_person相关表，稍后重新插入（以后来数据为准）
                            $flag["patent_id"]=(int)$result;
                            M('patent_other_inventor')->where($flag)->delete();
                            M('patent_extra_inventor')->where($flag)->delete();

                        }else{
                            //插入数据
                            $result = $patent->add($data);
                            //成功插入数据记录到日志中
                            $insert_counter++;
                            $inserted_id[]=$result;
                        }


                        if (empty($result))
                        {
                            $error_counter++;
                            $errored_name[]=$data["name"];
                        }else if($others_nos!=''){
                            // 插入其他发明人职工号
//                            dump($data);//显示添加的数据
                            $patent_id = (int)$result;
                            $others_no_list = explode('；',$others_nos);//此处分隔符需要选用全角分号，因为Excel中输入的可能是用的中文输入法
                            foreach($others_no_list as $person_no){
                                $person_id = getPersonIdByEmployeeNo($person_no);

                                if($person_id>0){
                                    $token["patent_id"] = $patent_id ;
                                    $token["person_id"]  = $person_id  ;
                                    $existed = M('patent_other_inventor')->where($token)->find();
                                    if($existed==null){
                                        //插入patent_other_inventor数据
                                        $patent_other_inventor_result = M('patent_other_inventor')->add($token);
                                    }
                                }else{//可能获取不到这个外键的id
                                    $error_counter++;
                                    $errored_name[]=$data["name"]."（其他发明人职工号 $person_no 不存在）";
                                    continue;
                                }
                            }
                        }else if($extra_names!=''){
                            // 插入其他校外发明人姓名
                            $patent_id = (int)$result;
                            $extra_name_list = explode('；',$extra_names);//此处分隔符需要选用全角分号，因为Excel中输入的可能是用的中文输入法
                            foreach($extra_name_list as $person_name){
                                    $token["patent_id"] = $patent_id ;
                                    $token["person_name"]  = $person_name  ;
                                    $patent_extra_inventor_result = M('patent_extra_inventor')->add($token);
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
                $descr = "\n 记录导入失败".$error_counter."条；\n ";
                if($error_counter>0){
                    //将插入失败的用户姓名记录下来
                    $descr .= "导入失败的专利名： ";
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