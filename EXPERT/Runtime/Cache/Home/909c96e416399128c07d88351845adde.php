<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Pragma" content="no-cache"> 
<meta http-equiv="Cache-Control" content="no-cache"> 
<meta http-equiv="Expires" content="0">
<title>高校专家信息管理系统</title>
<link rel="stylesheet" type="text/css" href="/Public/bootstrap/3.3.2/css/bootstrap.min.css">
<!-- <link media="screen" rel="stylesheet" type="text/css" href="/Public/angular-block-ui/0.2.2/angular-block-ui.min.css"> -->
<link media="screen" rel="stylesheet" type="text/css" href="/Public/css/nav.css">
<link media="screen" rel="stylesheet" type="text/css" href="/Public/css/base.css">
<link media="screen" rel="stylesheet" type="text/css" href="/Public/css/dateRange.css">
<link media="screen" rel="stylesheet" type="text/css" href="/Public/showLoading/css/showLoading.css">



</head>
<body>
	<div class="head-v3">
		<div class="navigation-up">
			<div class="navigation-inner">
				<div class="navigation-v3">
					<ul>
						<!-- <li style="margin-right:50px;font-size:22px;line-height:2.5;color:#000">南开大学专家信息管理系统</li> -->
						<li><img src="/Public/image/logo.gif" style="vertical-align:inherit!important" width="350" height="60"></li>
						<li class="nav-up-selected-inpage" _t_nav="home">
							<h2>
								<a href="/Homepage/index">首页</a>
							</h2>
						</li>
						<li class="" _t_nav="people">
							<h2>
								<a href="javascript:void(0);">科研队伍</a>
							</h2>
						</li>
						<li class="" _t_nav="project">
							<h2>
								<a href="javascript:void(0);">科研项目</a>
							</h2>
						</li>
						<!-- <li class="" _t_nav="funds">
							<h2>
								<a href="javascript:void(0);">科研经费</a>
							</h2>
						</li> -->
						<li class="" _t_nav="paper">
							<h2>
								<a href="javascript:void(0);">科研成果</a>
							</h2>
						</li>
						<li _t_nav="patent">
							<h2>
								<a href="javascript:void(0);">知识产权</a>
							</h2>
						</li>
						<li _t_nav="award">
							<h2>
								<a href="javascript:void(0);">成果获奖</a>
							</h2>
						</li>
						<li _t_nav="lab">
							<h2>
								<a href="/Lab/index">科研平台</a>
							</h2>
						</li>
						<li _t_nav="event">
							<h2>
								<a href="/Event/index">科技事件</a>
							</h2>
						</li>
						<li _t_nav="audit">
							<h2>
								<a href="/Audit/index">审计日志</a>
							</h2>
						</li>
						<li class="pull-right" _t_nav="logout">
							<h2>
								<a href="/Index/logout">退出</a>
							</h2>
						</li>
						<li class="pull-right" _t_nav="user">
							<h2>
								<a href="javascript:void(0);" style="padding:0 10px">你好，<?php echo $_SESSION['username'];?></a>
							</h2>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="navigation-down">
			<div id="people" class="nav-down-menu menu-1" style="display: none;" _t_nav="people">
				<div class="navigation-down-inner">
					<dl style="margin-left: 250px;">
						<dt>科研人员</dt>
						<dd>
							<a href="/People/index">人员列表</a>
						</dd>
						<dd>
							<a href="/People/query">人员查询</a>
						</dd>
						<dd>
							<a href="/People/import">批量导入</a>
						</dd>
						<!-- <dd>
							<a href="/People/detail">人员详情</a>
						</dd> -->
						<!-- <dd>
							<a href="javascript:void(0);">常用报表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">人员查重</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">荣誉称号</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">职称设置</a>
						</dd> -->
					</dl>
					<dl>
						<dt>组织结构</dt>
						<dd>
							<a href="javascript:void(0);">院系单位列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">其他单位列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">单位列表管理</a>
						</dd>

					</dl>
					<!--<dl>-->
				</div>
			</div>
			<div id="project" class="nav-down-menu menu-1" style="display: none;" _t_nav="project">
				<div class="navigation-down-inner">
					<!-- <dl>
						<dt>项目评审</dt>
						<dd>
							<a href="javascript:void(0);">评审计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">评审项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">评审查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">评审通知</a>
						</dd>
					</dl> -->
					<dl style="margin-left: 500px;">
						<dt>项目立项</dt>
						<dd>
							<a href="/Project/index">项目列表查询</a>
						</dd>
						<!-- <dd>
							<a href="javascript:void(0);">项目审核</a>
						</dd> -->
						<!--<dd>-->
							<!--<a href="javascript:void(0);">项目查询</a>-->
						<!--</dd>-->
						<dd>
							<a href="/Project/import">项目批量导入</a>
						</dd>
						<!-- <dd>
							<a href="javascript:void(0);">常用报表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">项目查重</a>
						</dd> -->
					</dl>
					<!-- <dl>
						<dt>项目变更</dt>
						<dd>
							<a href="javascript:void(0);">变更列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目中检</dt>
						<dd>
							<a href="javascript:void(0);">中检计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">中检查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目结项</dt>
						<dd>
							<a href="javascript:void(0);">结项计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">未结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">已结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">结项待审核</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl> -->
				</div>
			</div>
			<!-- <div id="funds" class="nav-down-menu menu-1" style="display: none;" _t_nav="funds">
				<div class="navigation-down-inner">
					<dl style="margin-left: 300px;">
						<dt>纵向经费</dt>
						<dd>
							<a href="javascript:void(0);">项目经费</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费认领</a>
						</dd>
					</dl>
					<dl>
						<dt>横向经费</dt>
						<dd>
							<a href="javascript:void(0);">项目经费</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">经费认领</a>
						</dd>
					</dl>
					<dl>
						<dt>项目变更</dt>
						<dd>
							<a href="javascript:void(0);">变更列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目中检</dt>
						<dd>
							<a href="javascript:void(0);">中检计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">中检查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl>
					<dl>
						<dt>项目结项</dt>
						<dd>
							<a href="javascript:void(0);">结项计划</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">未结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">已结项项目</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">结项待审核</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">预警列表</a>
						</dd>
					</dl>
				</div>
			</div> -->
			<div id="paper" class="nav-down-menu menu-1" style="display: none;" _t_nav="paper">
				<div class="navigation-down-inner">
					<dl style="margin-left: 600px;">
						<dt>论文成果</dt>
						<dd>
							<a href="/Paper/index">论文列表查看</a>
						</dd>
						<dd>
							<a href="/Paper/import">论文批量导入</a>
						</dd>
						<!-- <dd>
							<a href="javascript:void(0);">收录列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">历史论文</a>
						</dd> -->
					</dl>
					<!-- <dl>
						<dt>研究报告</dt>
						<dd>
							<a href="javascript:void(0);">研究报告列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">研究报告查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">研究报告查重</a>
						</dd>
					</dl>
					<dl>
						<dt>著作成果</dt>
						<dd>
							<a href="javascript:void(0);">著作列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作查重</a>
						</dd>
					</dl>
					<dl>
						<dt>鉴定成果</dt>
						<dd>
							<a href="javascript:void(0);">申请鉴定</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">鉴定列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">鉴定查重</a>
						</dd>
					</dl>
					<dl>
						<dt>成果登记</dt>
						<dd>
							<a href="javascript:void(0);">成果登记列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果登记查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果登记查重</a>
						</dd>
					</dl>
					<dl>
						<dt>成果转化</dt>
						<dd>
							<a href="javascript:void(0);">成果转化列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果转化查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">成果转化查重</a>
						</dd>
					</dl>
					<dl>
						<dt>分类维护</dt>
						<dd>
							<a href="javascript:void(0);">期刊源</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">转载名称</a>
						</dd>
					</dl> -->
				</div>
			</div>
			<div id="patent" class="nav-down-menu menu-1" style="display: none;" _t_nav="patent">
				<div class="navigation-down-inner">
					<dl style="margin-left: 700px;">
						<dt>专利成果</dt>
						<dd>
							<a href="/Patent/index">专利列表查询</a>
						</dd>
						<dd>
							<a href="/Patent/import">专利批量导入</a>
						</dd>
						<!--<dd>-->
							<!--<a href="javascript:void(0);">专利查询</a>-->
						<!--</dd>-->
						<!-- <dd>
							<a href="javascript:void(0);">专利查重</a>
						</dd> -->
					</dl>
					<!-- <dl>
						<dt>著作权</dt>
						<dd>
							<a href="javascript:void(0);">著作权列表</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作权查询</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作权查重</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">著作权类型</a>
						</dd>
					</dl> -->
				</div>
			</div>
			<div id="award" class="nav-down-menu menu-1" style="display: none;" _t_nav="award">
				<div class="navigation-down-inner">
					<dl style="margin-left: 800px;">
						<dt>成果获奖</dt>
						<dd>
							<a href="/Award/index">获奖列表查看</a>
						</dd>
						<dd>
							<a href="/Award/index">奖项批量导入</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">获奖查重</a>
						</dd>
					</dl>
				</div>
			</div>
		</div>
	</div>
	
  <!-- <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">添加人员信息</h4>
              </div>
              <div class="modal-body">
                  <form class="form-horizontal" action="/Home/Award/add" method="post">
                      <div class="form-group">
                          <label class="col-sm-2 control-label">姓名</label>
                          <div class="col-sm-9">
                              <input type="text" name="name" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">性别</label>
                          <div class="col-sm-9">
                            <input type="radio" value="男" name="gender" checked>男&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="女" name="gender">女
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">邮箱</label>
                          <div class="col-sm-9">
                            <input type="email" name="email" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">电话</label>
                          <div class="col-sm-9">
                            <input type="text" name="phone" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">职工号</label>
                          <div class="col-sm-9">
                            <input type="text" name="employee_no" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">博士后</label>
                          <div class="col-sm-9">
                            <input type="radio" value="1" name="postdoctor" checked>是&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="2" name="postdoctor">否
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">出生日期</label>
                          <div class="col-sm-9">
                            <input type="text" name="birthday" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">一级学科</label>
                          <div class="col-sm-9">
                            <input type="text" name="first_class" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-2 control-label">二级学科</label>
                          <div class="col-sm-9">
                            <input type="text" name="second_class" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-5">
                              <button type="submit" class="btn btn-info">确认</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div> -->
  <div class="container" ng-app="award" ng-controller="awardController as vm">
    <div class="col-sm-12 row">
      <show-more count="5">
        <div class="col-sm-12 bottom-10">
            <span class="cond-label">人员年龄：</span>
            <div class="btn-group">
                <date-range start-time="vm.params.person_startTime" end-time="vm.params.person_endTime"></date-range>
            </div>
        </div>
        <!-- <div class="col-sm-12 bottom-10">
            <span class="cond-label">获奖时间：</span>
            <div class="btn-group">
                <date-range start-time="vm.params.startTime" end-time="vm.params.endTime"></date-range>
            </div>
        </div> -->
        <div class="col-sm-12 bottom-10">
            <span class="cond-label">获奖级别：</span>
            <div class="btn-group">
                <label class="btn btn-default active" ng-model="vm.params.level" ng-click="vm.resetCheckbox(vm.level)" uib-btn-radio="''">全部</label>
                <label class="btn btn-default" ng-model="vm.level[0]" uib-btn-checkbox>一等奖</label>
                <label class="btn btn-default" ng-model="vm.level[1]" uib-btn-checkbox>二等奖</label>
                <label class="btn btn-default" ng-model="vm.level[2]" uib-btn-checkbox>三等奖</label>
            </div>
        </div>
        <div class="col-sm-12 bottom-10">
            <span class="cond-label">人员职称：</span>
            <div class="btn-group">
                <label class="btn btn-default active" ng-click="vm.resetCheckbox(vm.grade_id)" ng-model="vm.params.grade_id" uib-btn-radio="''">全部</label>
                <label class="btn btn-default" ng-repeat="item in vm.gradeList" ng-model="vm.grade_id[item.id]" uib-btn-checkbox ng-bind="item.name"></label>
            </div>
        </div>
        <div class="col-sm-12 bottom-10">
            <span class="cond-label">学术称号：</span>
            <div class="btn-group">            
                <label class="btn btn-default active" ng-click="vm.resetCheckbox(vm.academichonor_id)" ng-model="vm.params.academichonor_id" uib-btn-radio="''">全部</label>
                <label class="btn btn-default" ng-repeat="item in vm.academichonorList" ng-model="vm.academichonor_id[item.id]" uib-btn-checkbox data-toggle="tooltip" data-placement="top" title="{{item.name}}" ng-bind="item.name.length>4?item.name.substr(0,4) + '...':item.name"></label>
            </div>
        </div>
        <div class="col-sm-12">
            <span class="cond-label">学院：</span>
            <div class="btn-group">
              <label style="margin-bottom:0;color:#eee">
                <select class="form-control" ng-model="vm.collegeSelected" ng-options="item.id as item.name for item in vm.collegeList">
                  <option value="">全部</option>
                </select>
              </label>
              <label style="margin-bottom:0" ng-repeat="item in vm.collegeSelectedList">
                <button tyle="button" class="btn button-link" ng-bind="vm.collegeMap[item]+'   X'" ng-click="vm.removeCollege(item)"><b></b></button>&nbsp;
              </label>
            </div>
        </div>
        <div class="col-sm-12 bottom-10 top-10">
            <span class="cond-label">人员姓名：</span>
            <div class="btn-group">
              <input type="text" class="form-control" ng-model="vm.person_name"/>
            </div>
        </div>
        <div class="col-sm-12 bottom-10">
            <span class="cond-label">奖励名称：</span>
            <div class="btn-group">
              <input type="text" class="form-control" ng-model="vm.name"/>
            </div>
        </div>
        <div class="col-sm-12">
          <span class="cond-label"></span>
            <button class="btn btn-info" ng-click="vm.submit()">确定</button>
            <button class="btn btn-default" ng-click="vm.cancel()">取消</button>
        </div>
        <div class="col-sm-12" id="more">
            <span class="glyphicon glyphicon-triangle-bottom">
            </span>
        </div>
      </show-more>
  </div>
  	<div class="col-md-12">
      <div class="panel panel-default table-header">
        <div class="panel-heading table-heading">
          <div class="row">
            <div class="col-md-4 pull-left">
              <h3 class="panel-title table-title">获奖列表</h3>
            </div>
            <div class="col-md-8 pull-right" ng-hide="vm.showCheckbox">
                <!-- <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#modal">新增&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></span></button> -->
                <button type="button" class="btn btn-info pull-right right-10" ng-click="vm.showCheckbox=true">导出Excel&nbsp;&nbsp;<span class="glyphicon glyphicon-export"></span></button>
                <div class="input-group col-md-4 pull-right right-10">
                  <input type="text" class="form-control" ng-model="filter" placeholder="输入关键字">
                  <div class="input-group-addon">筛选</div>
                </div>
            </div>
            <div class="col-md-8" ng-show="vm.showCheckbox">
                <button type="button" class="btn btn-default pull-right" ng-click="vm.showCheckbox=false">取消导出</span></button>
                <button type="button" class="btn btn-primary pull-right right-10" ng-click="vm.exportExcel('all')">导出所有<span class="glyphicon glyphicon-export"></span></button>
                <button type="button" class="btn btn-success pull-right right-10" ng-click="vm.exportExcel('current')">导出当前页<span class="glyphicon glyphicon-export"></span></button>
                <h3 class="table-title warning-text pull-left">请选择要导出的字段</h3>
            </div>
          </div>
        </div>
      </div>
  		<table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th><span ng-hide="vm.showCheckbox">#</span><span ng-show="vm.showCheckbox">全选<input type="checkbox" ng-click="vm.selectAll();"></span></th>
            <th>姓名<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.person_name"></th>
            <th>出生日期<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.birthday"></th>
            <th>学院<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.col_name"></th>
            <th>职称<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.grade_name"></th>
            <th>学术荣誉<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.academichonor_name"></th>
            <th>奖励名称<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.name"></th>
            <th>奖励级别<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.level"></th>
            <th>获奖时间<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.time"></th>
            <th>备注<input type="checkbox" ng-show="vm.showCheckbox" ng-model="vm.exportParams.comment"></th>
          </tr>
        </thead>
        <tbody>
          <tr ng-if="vm.paginationConf.totalItems" ng-repeat="item in vm.awardList | filter:filter">
            <td ng-bind="item.id"></td>
            <td ng-bind="item.person_name"></td>
            <td ng-bind="item.birthday"></td>
            <td ng-bind="item.col_name"></td>
            <td ng-bind="item.grade_name"></td>
            <td ng-bind="item.academichonor_name"></td>
            <td ng-bind="item.name"></td>
            <td ng-bind="item.level"></td>
            <td ng-bind="item.time"></td>
            <td ng-bind="item.comment"></td>
          </tr>
          <tr ng-if="!vm.paginationConf.totalItems"><td colspan="12"><h2 class="text-center text-danger">无数据</h2></td></tr>
        </tbody>
      </table>
      <tm-pagination conf="vm.paginationConf"></tm-pagination>
    </div>
  </div>

</body>

<script src="/Public/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>
<script src="/Public/bootstrap/3.3.2/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/Public/angular.js/1.3.0/angular.min.js" type="text/javascript"></script>
<!-- <script type="text/javascript" src="/Public/angular-block-ui/0.2.2/angular-block-ui.min.js"></script> -->
<script type="text/javascript" src="/Public/showLoading/js/jquery.showLoading.min.js"></script>
<script type="text/javascript" src="/Public/js/app.module.js"></script>
<script type="text/javascript" src="/Public/js/app.service.js"></script>
<script type="text/javascript" src="/Public/js/dateRange.js"></script>
<script type="text/javascript" src="/Public/js/utils.directive.js"></script>
<script type="text/javascript" src="/Public/js/tm.pagination.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	var qcloud={};
	$('[_t_nav]').hover(function(){
		var _nav = $(this).attr('_t_nav');
		clearTimeout( qcloud[ _nav + '_timer' ] );
		qcloud[ _nav + '_timer' ] = setTimeout(function(){
		$('[_t_nav]').each(function(){
		$(this)[ _nav == $(this).attr('_t_nav') ? 'addClass':'removeClass' ]('nav-up-selected');
		});
		$('#'+_nav).stop(true,true).slideDown(200);
		}, 150);
	},function(){
		var _nav = $(this).attr('_t_nav');
		clearTimeout( qcloud[ _nav + '_timer' ] );
		qcloud[ _nav + '_timer' ] = setTimeout(function(){
		$('[_t_nav]').removeClass('nav-up-selected');
		$('#'+_nav).stop(true,true).slideUp(200);
		}, 150);
	});
});
function selectMenu(first, second, third){
	$("li[_t_nav]").each(function(index, value){
		if ($(this).attr('_t_nav') == first) $(this).addClass('nav-up-selected-inpage');
		else $(this).removeClass('nav-up-selected-inpage');
	});
	$('#'+first).find('dl').eq(second).find('a').eq(third).addClass('third-menu-selected');
}
</script>

<script type="text/javascript" src="/Public/js/award.controller.js"></script>
<script type="text/javascript" src="/Public/angular-ui-bootstrap/0.14.3/ui-bootstrap.min.js"></script>
<script>
  selectMenu('award', 0, 0);  
</script>

</html>