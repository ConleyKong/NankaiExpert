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

	        $paper = D('PaperView');
            unset($param['pub_start']);
            unset($param['pub_end']);
            unset($param['page']);
            unset($param['items']);
	        $result = $paper->where($param)->page($pageNum,$itemsNum)->order('paper.id')->select();
            $totalNum = $paper->where($param)->count();
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


    public function import(){
        if (!session('logged')) {
            $this->redirect('Index/index');
        } else {
            //如果文件非空
            if (! empty ( $_FILES ['file_paper'] ['name'] ))
            {
                //导入person表
                $tmp_file = $_FILES ['file_paper'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['file_paper'] ['name'] );
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
                         * 论文名（name）	第一作者职工号(first_author_id->person(employee_num))	发表日期(publish_date)	联系作者职工号(contact_author_id->person(employee_num))
                         * 论文类型(paper_type)	期刊/会议名称(conference_name)	其他作者（学院），多个作者中间用分号隔开(other_author)，	学院名(college_id->college(name))
                         *  需要特殊处理的字段：第一作者id，学院id，联系作者id
                         */
                        $paper = M("paper");
                        $data = array();
                        $data["name"]= $v[0];
//                       $v[1];第一作者id
                        $data["publish_date"]=$v[2];
//                       $v[3];联系作者id
                        $data["paper_type"]=$v[4];
                        $data["conference_name"]=$v[5];
                        $data["other_author"]=$v[6];
//                       $v[7];学院id

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 处理第一作者信息 person
                        $first_author_id = getPersonIdByEmployeeNo($v[1]);
                        if($first_author_id>0){
                            $data["first_author_id"] = $first_author_id;
                        }//else:id信息获取失败

                        //处理联系作者信息 person

                        $contact_author_id = getPersonIdByEmployeeNo($v[3]);
                        if($contact_author_id>0){
                            $data["contact_author_id"] = $contact_author_id;
                        }//else:id信息获取失败

                        //处理学院id college
                        $college_id = getForeignKey($v[7],"college",$college_buffer);
                        if($college_id>0){
                            $data["college_id"] = $college_id;
                        }//else:id信息获取失败

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
//                    $this->success("恭喜您，成功导入或更新数据"+($insert_counter+$update_counter)+"条！（详情见日志）",'',5);
                    $this->success("恭喜您，科研成果数据导入操作成功！（详情见审计模块）",'',4);
                }else{
                    $this->error("存在导入失败的记录，请查看审计模块的日志进行修正！",'',4);
                }

            }

            $this->error("文件选择失败！");

        }
    }

}