<?php
return array(
	//'配置项'=>'配置值'
	'DB_HOST' => 'localhost', // 服务器地址
	'DB_NAME' => 'expert', // 数据库名
	'DB_USER' => 'root', // 数据库用户名
	'DB_PWD'  => '', // 数据库密码
	'DB_TYPE' =>'mysql',
	'SHOW_PAGE_TRACE'=>true,//开启页面trace
	'SHOW_DB_TIMES' =>true,
	'DB_SQL_LOG' =>TRUE,
	//模板渲染转义
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__' => __ROOT__.'/Public',
		'__CDN__' => __ROOT__.'/Public',
	),

	'URL_PARAMS_BIND' => TRUE,
	'URL_MODEL'       => 2,
	'URL_HTML_SUFFIX' => '',
	'MODULE_ALLOW_LIST' => array('Home'),
	'DEFAULT_MODULE' => 'Home', // 默认模块
	'DEFAULT_FILTER' => 'htmlspecialchars',
	//后端管理账户密码
	'ADMIN_ACCOUNT' => 'nankai',
	'ADMIN_PWD' => 'litao',
);