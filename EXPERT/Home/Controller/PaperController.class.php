<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Conley.K
 * Date: 2017/2/17 0017
 * Time: 16:29
 */
class PaperController extends Controller {
    public function index(){
        if (! session('logged')){
            $this->redirect('Index/index');
        }
        else{
            $this->display();
        }
    }


    public function paperList()
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

            $publish_date = array();
            if($param['start_time']){
                $pub_start = $param['start_time'];
                $publish_date = array('gt',$pub_start);
                unset($param['startTime']);
            }
            if($param['end_time']){
                $pub_end = $param['end_time'];
                $publish_date = array('lt',$pub_end);
                unset($param['endTime']);
            }
            if($publish_date){
                $query['publish_date'] = $publish_date;
            }

            if ($param['name']){
                $query['name']=array('like','%'.$param['name'].'%');
                unset($param['name']);
            }
            if ($param['conference_name']){
                $query['conference_name']=array('like','%'.$param['conference_name'].'%');
                unset($param['conference_name']);
            }
            if ($param['person_name']){
                $query['person_name']=array('like','%'.$param['person_name'].'%');
                unset($param['person_name']);
            }


            $string = '';
            if ($param['college_id']){
                $string .= $string ? ' AND ('.$param['college_id'].')' : '('.$param['college_id'].')';
                unset($param['college_id']);
            }
            if($param['keyword']){
                $keyword = $param['keyword'];
                $ts = "( paper.name like '%$keyword%' OR paper.conference_name like '%$keyword%' OR person.name like '%$keyword%')";
                $string .= $string?' AND '.$ts:$ts;
                unset($param['keyword']);
            }
            if ($string){
                $query['_string'] = $string;
            }

            $paper = D('PaperView');

            $query["valid"]=true;

            $result = $paper
                ->where($query)
                ->page($pageNum,$itemsNum)
                ->order('paper.id')
                ->select();
            $totalNum = $paper->where($query)->count();
            $result[0]['totalNum'] = $totalNum;
            //操作记录日志
//            $audit['name'] = session('username');
//            $audit['ip'] = getIp();
//            $audit['module'] = '论文列表';
//            $audit['time'] = date('y-m-d h:i:s',time());
//            $audit['result'] = '成功';
//            M('audit')->add($audit);
            $this->ajaxReturn($result,'json');
        }
    }


    /*
    * 删除指定paper
    * 将paper的valid置为false
    */
    public function delete(){

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


            $pub_start = I('get.start_time');
            $pub_end = I('get.end_time');
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
            $keyword = I('get.keyword');
            if($keyword){
                $ts = " (paper.name like '%$keyword%' OR paper.conference_name like '%$keyword%' OR person.name like '%$keyword%')";
                $string .= $string?' AND '.$ts:$ts;
            }
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
                        0 收录年份
                        1 论文题目
                        2 论文类型
                        3 中文第一作者
                        4 中文其他作者(半角逗号分割，英文名中间为句点)
                        5 英文作者
                        6 通讯作者
                        7 认领单位
                        8 出版时间
                        9 发表刊物
                        10 影响因子
                        11 文章类型
                        12 会议
                        13 刊号 isbn
                        14 卷号 vol
                        15 期号 issue
                        16 页码范围 page range
                         *  需要特殊处理的字段：第一作者id，学院id，联系作者id
                         */
                        $paper = M("paper");
                        $data = array();
                        $name = $v[1];//论文名（name）☑
                        $data["name"]= $name;
                        $first_author = trim($v[3]);
                        $data['first_author'] = $first_author;//第一作者姓名 ☑
                        $other_authors_name = $v[4];//其他作者姓名（半角逗号分割）
                        $data['other_authors_name'] = $other_authors_name;//其他作者姓名（半角逗号分割）☑
                        $data['english_authors'] = $v[5];//所有作者的英文名☑
                        $contact_author = $v[6];
                        $data['contact_author'] = $contact_author;//联系作者姓名☑
                        $college_name = $v[7];
                        $publish_year=trim(str_replace(array("\'",","),'',$v[8]));//出版时间(publish_year)☑
                        $data["publish_year"]=$publish_year;
                        $conference_name = str_replace(array("\'",","),'',$v[9]);//期刊名称(conference_name)
                        $data["conference_name"]=$conference_name;
                        $data["article_type"]=$v[11];//文章类型(article_type)
                        $data['isbn']=trim($v[13]);//isbn
                        $issue = trim(str_replace('n','',$v[15]));//期号
                        $data['issue'] = $issue;
                        $page_range = trim(str_replace('p','',$v[16]));//页码范围
                        $data['page_range'] = $page_range;

                        //有关paper——type的输入
                        $record_year=trim(str_replace(array("\'",","),'',$v[0]));//收录年份
                        $paper_type = $v[2];//论文类型;
                        $factor=$v[10];//影响因子
                        $conference=$v[12];//会议名称

                        ///////////////////////////////////////////////////////////////////////
                        //\\\\\涉及外键的操作////\\

                        // 0.学院id
                        $college_id = getForeignKeyFromDB($college_name,"college");
                        if($college_id>0){
                            $data["college_id"] = $college_id;
                        }else{
                            $error_counter++;
                            $errored_name[]= " 学院信息：".$college_name."不存在；";
                            continue;
                        }

                        // 1.第一作者id
                        $first_author_id = getPidByNameAndCollege($first_author,$college_id);
                        if($first_author_id>0){
                            $data["first_author_id"] = $first_author_id;
                        }else{
                            $data["first_author_id"] = 88888;
                        }
//                        else{
//                            $error_counter++;
//                            $errored_name[]= "职工".$first_author."不存在；";
////                            continue;
//                        }

                        // 2.联系作者id
                        $contact_author_id = getPidByNameAndCollege($contact_author,$college_id);
                        if($contact_author_id>0){
                            $data["contact_author_id"] = $contact_author_id;
                        }else{
                            $data["contact_author_id"] = 88888;
                        }
//                        else{
//                            $error_counter++;
//                            $errored_name[]=" 联系作者".$contact_author."不存在）";
////                            continue;
//                        }

                        /*
                        *   查重：
                         * 使用两种思路：
                         * 1.第一作者和出版时间一样以及论文名一样，
                         * 2.第一作者和出版时间一样以及期号和页码一样
                        */
                        $search_data=array();
                        //去掉英文第一作者的姓，因为不同索引的姓缩写方式不同
                        $search_data["first_author"]=substr($first_author,0,strpos($first_author,'.'));
                        $search_data["publish_year"]=$publish_year;
                        $temp_paper = $paper->where($search_data)->select();

                        $paper_id = -1;
                        if($temp_paper!=null){
                            foreach ($temp_paper as $p){
                                if($p['name']==$name){//论文名相同
                                    $paper_id = $p['id'];
                                    break;
                                }
                                //论文所在期号和页码相同
                                if($p['issue']==$issue && $p['page_range']==$page_range){
                                    $paper_id = $p['id'];
                                    break;
                                }
                            }
                        }

                        //不存在重名的论文，直接插入paper表、paper_type表和other——authors表
                        if($paper_id<0){
                            //paper中插入数据
                            $data['paper_type']=$paper_type;
                            $result = $paper->add($data);
                            $paper_id =  $result;
                            $insert_counter++;//成功插入数据记录到日志中
                            $inserted_id[]=$result;
                        }else{//存在重名的paper
                            //只更新paper数据
                            $condition = array();
                            $condition['id']=$paper_id;
                            $o_paper = $paper->where($condition)->find();
                            if(strpos($o_paper['paper_type'],$paper_type) === false){//若先前没有这个类型
                                $o_paper['paper_type'] = $o_paper['paper_type'].','.$paper_type;
                            }
                            $num = $paper->where($condition)->save($o_paper);
                            //将成功更新的数据记录到日志中
                            $update_counter++;
                            $updated_id[]=$paper_id;
                        }
                        //paper_type中插入数据
                        $type_data = array();
                        $type_data['paper_id']=$paper_id;
                        $type_date['type_name']=$paper_type;
                        $type_data['record_year']=$record_year;
                        $cname = $conference!=''?$conference:$conference_name;
                        $type_data['conference_name']= $cname;
                        $type_data['factor'] = $factor;
                        M('paper_type')->add($type_data);

                        //paper_other_authors中插入数据
                        $authors = explode(',',$other_authors_name);
                        foreach ($authors as $a){
                            if($a!=''){
                                $oa = array();
                                $oa_id = getPidByNameAndCollege($a,$college_id);
                                if($oa_id>0){
                                    $oa['paper_id']=$paper_id;
                                    $oa['person_name']=$a;
                                    $oa["person_id"] = $oa_id;
                                    M('paper_other_authors')->add($oa);
                                }
                            }
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