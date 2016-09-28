<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index()
    {
    	if ( ! session('logged'))
		{
			$this->display();
		}
		else
		{
			$this->redirect('Homepage/index');
		}
    }

	public function login()
	{
		$condition['account'] = I('post.account', null, 'string');
		$condition['password'] = md5(I('post.password', null, 'string'));
		//$condition['password'] = md5($_POST['password']);
		if (I('post.captcha') != session('captcha'))
		{
			$code = 0;
			$msg = "验证码错误";
			$audit['result'] = '失败';
			$audit['descr'] = '验证码错误';
		}else{
			$user=M('user')->where($condition)->find();
			
			if ($user) {
				if($user['valid']==0){
					$code = 0;
					$msg = '用户已被锁定';
					$audit['result']='失败';
					$audit['descr']='用户被锁定';
				}else{
					session('logged', '1');
					session('username', $condition['account']);
					session('role_num',$user['role_id']);
					$audit['result'] = '成功';
					$code = 1;
					$msg = '成功';
				}

			}
			else
			{
	            $audit['result'] = '失败';
	            $audit['descr'] = '用户名密码错误';
				$code = 0;
				$msg = '用户名密码错误';
			}
		}

		$audit['name'] = $condition['account'];
		$audit['ip'] = getIp();
		$audit['module'] = '登录';
		$audit['time'] = date('y-m-d h:i:s',time());
		M('audit')->add($audit);
		$this->ajaxReturn(array('code' =>$code,'msg' => $msg));

	}

	public function logout()
	{
		$audit['name'] = session('username');
        $audit['ip'] = getIp();
        $audit['module'] = '退出登录';
        $audit['time'] = date('y-m-d h:i:s',time());
        $audit['result'] = '成功';
        M('audit')->add($audit);
		session(null);
		$this->redirect('index');
	}

	public function register()
	{
		if (I('post.captcha2') != session('captcha2'))
		{
			$this->error('验证码错误！');
		}
		else
		{
			$username = I('post.name', null, 'string');
			$password = I('post.pwd', null, 'string');
			$data['account'] = $username;
			$data['password'] = md5($password);
			$result = M('user')->add($data);
			if ($result)
			{
				session('username', $username);
				session('logged', '1');
				$this->success('注册成功');
			}
			else
			{
				$this->error('注册失败');
			}
		}
	}

	//验证码
	public function captcha()
	{
		$image = imagecreatetruecolor(130,40);
		$bgcolor = imagecolorallocate($image,255,255,255);
		imagefill($image,0,0,$bgcolor);
		//$code = '';
		$result = rand(10,20);
		$num = rand(0,9);
		$content = array($num,'+','?','=',$result);
		for ($i = 0;$i < 5;$i++)
		{
			$fontsize = 5;
			$fontcolor = imagecolorallocate($image,rand(0,120),rand(0,120),rand(0,120));
			//$str = 'abcdefghijkmnpqrstuvwxyz23456789';
			$fontcontent = $content[$i];//substr($str,rand(0,strlen($str)-1),1);
			//$code .= $fontcontent;
			$x = ($i * 90 / 5) + rand(5,10);
			$y = rand(10,20);
			imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
		}
		session('captcha',$result - $num);
		for ($i = 0;$i < 500;$i++)
		{
			$pointcolor = imagecolorallocate($image,rand(50,200),rand(50,200),rand(50,200));
			imagesetpixel($image,rand(1,99),rand(1,99),$pointcolor);
		}
		for ($i = 0;$i < 3;$i++)
		{
			$linecolor = imagecolorallocate($image,rand(80,220),rand(80,220),rand(80,220));
			imageline($image,rand(1,99),rand(1,29),rand(1,99),rand(1,29),$linecolor);
		}
		header('content-type:image/png');
		imagepng($image);
	}

	public function captcha2()
	{
		$image = imagecreatetruecolor(130,40);
		$bgcolor = imagecolorallocate($image,255,255,255);
		imagefill($image,0,0,$bgcolor);
		//$code = '';
		$result = rand(10,20);
		$num = rand(0,9);
		$content = array($num,'+','?','=',$result);
		for ($i = 0;$i < 5;$i++)
		{
			$fontsize = 5;
			$fontcolor = imagecolorallocate($image,rand(0,120),rand(0,120),rand(0,120));
			//$str = 'abcdefghijkmnpqrstuvwxyz23456789';
			$fontcontent = $content[$i];//substr($str,rand(0,strlen($str)-1),1);
			//$code .= $fontcontent;
			$x = ($i * 90 / 5) + rand(5,10);
			$y = rand(10,20);
			imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
		}
		session('captcha2',$result - $num);
		for ($i = 0;$i < 500;$i++)
		{
			$pointcolor = imagecolorallocate($image,rand(50,200),rand(50,200),rand(50,200));
			imagesetpixel($image,rand(1,99),rand(1,99),$pointcolor);
		}
		for ($i = 0;$i < 3;$i++)
		{
			$linecolor = imagecolorallocate($image,rand(80,220),rand(80,220),rand(80,220));
			imageline($image,rand(1,99),rand(1,29),rand(1,99),rand(1,29),$linecolor);
		}
		header('content-type:image/png');
		imagepng($image);
	}
}