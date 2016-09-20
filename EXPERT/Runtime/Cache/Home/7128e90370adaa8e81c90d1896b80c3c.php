<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <title>南开大学专家信息管理系统</title>
  <link rel="stylesheet" type="text/css" href="/Public/bootstrap/3.3.2/css/bootstrap.min.css">
</head>
<body>
  <div class="modal fade" id="register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">注册账号</h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="post" action="/Home/Index/register">
            <div class="form-group">
                <label class="col-sm-2 control-label">账号：</label>
                <div class="col-sm-6">
                  <input type="text" value="" class="form-control" name="name" id="name" placeholder="账号" required/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">密码：</label>
                <div class="col-sm-6">
                  <input type="password" value="" class="form-control" name="pwd" id="pwd" placeholder="密码" required/>
                </div>
            </div>
            <div class="form-group">
                  <label class="col-sm-2 control-label">验证码：</label>
                  <div class="col-sm-6">
                    <input type="text" name="captcha2" class="form-control" style="float:left;width:50%" placeholder="验证码" required/>
                    <img src="/Home/Index/captcha2?r=<?php echo rand();?>" style="float:left;width:100px;margin-left:10px;cursor:pointer" onclick="this.src=this.src+'?'+Math.random();">  
                  </div>    
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">提交</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="container" style="background-color:#FFFFFF;width:930px;border:8px solid #1681D3;margin-top:80px;padding-left:0;display:flex">
    <div class="col-md-6" style="padding-left:0">
      <img src="/Public/image/login.jpg"/>
    </div>
    <div class="col-md-offset-2 col-md-4" style="margin-top:50px;padding-right:30px">
      <form class="form-horizontal" method="post" action="/Home/Index/login">
        <div class="form-group has-feedback">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <input type="text" value="" class="form-control" name="username" id="username" placeholder="账号" required/>
        </div>
        <div class="form-group has-feedback">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <input type="password" value="" class="form-control" name="password" id="password" placeholder="密码" required/>
        </div>
        <div class="form-group"> 
         <!--  <label class="col-sm-2 control-label">验证码</label> -->
              <input type="text" name="captcha" id="captcha" class="form-control" style="float:left;width:50%" placeholder="验证码" required/>
              <img src="/Home/Index/captcha?r=<?php echo rand();?>" style="float:left;width:100px;margin-left:10px;cursor:pointer" onclick="this.src=this.src+'?'+Math.random();">      
        </div>
        <div class="form-group">
            <button type="button" onclick="login()" class="btn btn-primary btn-block">登录</button>
            <div class="pull-right">
              <a href="javascript:void(0);" data-toggle="modal" data-target="#register" class="btn btn-link">还没有账号？点此注册</a>
            </div>
        </div>
        <div class="text-center" id='error_area' style="color:red;display:none">
          <h4 id='error_text'></h4>
        </div>
      </form>
    </div>
  </div>
    <script type="text/javascript" src="/Public/jquery/2.1.1/jquery.min.js"></script>
  	<script type="text/javascript" src="/Public/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  	<script type="text/javascript">
      document.onkeydown = function(event){ 
        e = event ? event :(window.event ? window.event : null); 
        if(e.keyCode == 13) login();
      }
      function login(){
        var username = jQuery.trim($('#username').val());
        var password = jQuery.trim($('#password').val());
        var captcha = jQuery.trim($('#captcha').val());
//        alert('/Public/jquery/2.1.1/');
        $.post('<?php echo U("Index/login");?>', {account:username,password:password,captcha:captcha},function(resp){
            if (resp.code == '0') {
              $('#error_area').fadeIn();
              $('#error_text').text(resp.msg);
            } else if (resp.code == '1') location.href = '/Homepage/index';
          }
        );
      }
  	</script>
</body>
</html>