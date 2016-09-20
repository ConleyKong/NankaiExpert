## 代码目录结构
```
|─EXPERT        专家库模型-视图-控制器代码目录
|    |─Common       公共库目录
|    |    └─Conf                 配置文件目录           
|    |         └─conf.php                       配置文件
|    |—Templates
|    |   └─nav.html              顶部菜单
|    └─Home         主模块目录
|        |─Conf                  配置文件
|        |─Common                公共函数
|        |─Controller            控制器目录(后端API接口)
|        |    |─IndexController.class.php       用户登录
|        |    |─HomepageController.class.php    首页(默认)
|        |    |─PeopleController.class.php      科研队伍
|        |    |─ProjectController.class.php     科研项目
|        |    |─PaperController.class.php       科研成果
|        |    |─PatentController.class.php      知识产权
|        |    |—AwardController.class.php       成果获奖
|        |    └─AuditController.class.php       审计日志
|        |─Model                 模型目录(操作数据库)
|        |    |─PeopleViewModel.class.php       人员数据表
|        |    |─ProjectViewModel.class.php      项目数据表
|        |    |─PaperViewModel.class.php        论文数据表
|        |    |—PatentViewModel.class.php       专利数据表
|        |    └─AwardViewModel.class.php        奖励数据表
|        └─View                  视图目录(前端html文件)
|             |─Index                           登录页
|             |─Homepage                        首页
|             |─People                          科研队伍菜单下的页面
|             |─Project                         科研项目
|             |—Paper                           科研成果
|             |─Patent                          知识产权
|             |—Award                           成果获奖
|             └─Audit                           审计日志
|
|─Public       前端js、css文件目录
|    |─css           css文件目录
|    |─js            自定义javascript目录
|    |    |─widget                小插件目录
|    |    |─templates             插件所需的html模板
|    |    └─XXX.controller.js     这类文件为angular的控制器
|    └─...           各种第三方文件(如angular、jQuery)
|
|—ThinkPHP     官方框架目录(不需要修改)
```

## 部分功能实现方法
### 各个列表页的查询
前端html绑定变量，并在angular控制器中监控
比如，在person.controller.js中监控vm.params
```
$scope.$watch('vm.params', function(){
    getPersonList(vm.params);
}, true);
```
在html中，用ng-model绑定相应的变量
```
<div class="col-sm-3 bottom-10">
    <span class="cond-label">性别：</span>
    <div class="btn-group">
        <label class="btn btn-default active" ng-model="vm.params.gender" uib-btn-radio="''">全部</label>
        <label class="btn btn-default" ng-model="vm.params.gender" uib-btn-radio="'男'">男</label>
        <label class="btn btn-default" ng-model="vm.params.gender" uib-btn-radio="'女'">女</label>
    </div>
</div>
<div class="col-sm-3 bottom-10">
    <span class="cond-label">博士后：</span>
    <div class="btn-group">
        <label class="btn btn-default active" ng-model="vm.params.postdoctor" uib-btn-radio="''">全部</label>
        <label class="btn btn-default" ng-model="vm.params.postdoctor" uib-btn-radio="'1'">是</label>
        <label class="btn btn-default" ng-model="vm.params.postdoctor" uib-btn-radio="'0'">否</label>
    </div>
</div>
```
### 导出文件
以person.controller.js为例
```
vm.showCheckbox = false;
vm.exportParams = {name:false,gender:false,employee_no:false,college:false,postdoctor:false,aca_name:false,birthday:false,email:false,phone:false,first_class:false,second_class:false};

vm.exportExcel = exportExcel;
function exportExcel(type){
 var field = [];
 angular.forEach(vm.exportParams, function(value, key){
 	if (value) field.push(key);
 });
 if (field.length)
	field = field.join(',');
 else{
 	alert('请至少选择一个字段');
 	return;
 }
 var url = '/People/export/?type=' + type + '&query=' + vm.params + '&field=' + field;
location.href = url;
 vm.showCheckbox = false;
}
* 大括号独占一行：
```
以上代码中，vm.exportParams表示所有可以导出的字段，初始所有值为false，选择后对应的值为true，循环即可得到用户所选择的字段
导出报表共需要三个参数：导出类型type(当前页或所有)、查询条件query、导出字段field
后端API接口代码如下：
```
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
        if ($query['startTime'] && $query['endTime'])
            $query['birthday'] = array(array('gt',$query['startTime']),array('lt',$query['endTime']));
        if ($query['college_id']){
            $string .= $string ? ' AND ('.$query['college_id'].')' : '('.$query['college_id'].')';
        }
        $person = D('PeopleView');
        $query = objectToArray($query);
        unset($query['page']);
        unset($query['items']);
        unset($query['startTime']);
        unset($query['endTime']);
        if ($type == 'all'){
            $audit['descr'] = '导出所有。';
            $result = $person->field($field)->where($query)->order('person.id')->select();
        }
        else if ($type == 'current'){
            $audit['descr'] = '导出当前。';
            $result = $person->field($field)->where($query)->page($pageNum,$itemsNum)->order('person.id')->select();
        }
        else
            return '未知错误';
        $titleMap = array('name'=>'姓名','gender'=>'性别','employee_no'=>'职工号','col_name'=>'学院','postdoctor'=>'博士后','aca_name'=>'荣誉称号','birthday'=>'出生日期','email'=>'邮箱','phone'=>'电话','first_class'=>'一级学科','second_class'=>'二级学科');
        $field = split(',', $field); 
        $excelTitle = array();
        foreach ($field as $value) {
            array_push($excelTitle, $titleMap[$value]);
        }
        $filename = '人员信息';
        //审计日志
        $audit['name'] = session('username');
        $audit['ip'] = getIp();
        $audit['module'] = '人员列表';
        $audit['time'] = date('y-m-d h:i:s',time());
        $audit['result'] = '成功';
        $audit['descr'] .= '导出人员列表';
        M('audit')->add($audit);
        exportExcel($filename, $excelTitle, $result);
    }
}
```
### 画统计图
根据查询条件查出数据后使用构造Echarts option画出相应的图，需要查看echarts文档

