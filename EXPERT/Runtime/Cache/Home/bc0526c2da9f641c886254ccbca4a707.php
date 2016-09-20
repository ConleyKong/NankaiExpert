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
								<!--<a href="javascript:void(0);">科研队伍</a>-->
								<a href="/People/index">科研队伍</a>
							</h2>
						</li>
						<li class="" _t_nav="project">
							<h2>
								<a href="/Project/index">科研项目</a>
							</h2>
						</li>
						<!-- <li class="" _t_nav="funds">
							<h2>
								<a href="javascript:void(0);">科研经费</a>
							</h2>
						</li> -->
						<li class="" _t_nav="paper">
							<h2>
								<a href="/Paper/index">科研成果</a>
							</h2>
						</li>
						<li _t_nav="patent">
							<h2>
								<a href="/Patent/index">知识产权</a>
							</h2>
						</li>
						<li _t_nav="award">
							<h2>
								<a href="/Award/index">成果获奖</a>
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
							<a href="/Award/import">奖项批量导入</a>
						</dd>
						<dd>
							<a href="javascript:void(0);">获奖查重</a>
						</dd>
					</dl>
				</div>
			</div>
			<div id="lab" class="nav-down-menu menu-1" style="display: none;" _t_nav="lab">
				<div class="navigation-down-inner">
					<dl style="margin-left: 800px;">
						<dt>科研平台</dt>
						<dd>
							<a href="/Lab/index">科研平台列表</a>
						</dd>
						<dd>
							<a href="/Lab/import">平台批量导入</a>
						</dd>

					</dl>
				</div>
			</div>
		</div>
	</div>
	
  <div class="container" ng-app="audit" ng-controller="auditController as vm">
  	<div class="col-md-12">
      <div class="panel panel-default table-header">
        <div class="panel-heading table-heading">
          <div class="row">
            <div class="col-md-4 pull-left">
              <h3 class="panel-title table-title">审计列表</h3>
            </div>
          </div>
        </div>
      </div>
  		<table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>用户名</th>
            <th>IP地址</th>
            <th>模块</th>
            <th>时间</th>
            <th>结果</th>
            <th>描述</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-if="vm.paginationConf.totalItems" ng-repeat="item in vm.auditList | filter:filter">
            <td ng-bind="item.id"></td>
            <td ng-bind="item.name"></td>
            <td ng-bind="item.ip"></td>
            <td ng-bind="item.module"></td>
            <td ng-bind="item.time"></td>
            <td ng-bind="item.result"></td>
            <td ng-bind="item.descr"></td>
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

<script type="text/javascript" src="/Public/js/audit.controller.js"></script>
<script type="text/javascript" src="/Public/angular-ui-bootstrap/0.14.3/ui-bootstrap.min.js"></script>
<script>
  selectMenu('audit', 6, 0);
</script>

</html>