<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index()
    {
    	if( !session('logged')){
			$this->display();
		}else{
			$this->redirect('Homepage/index');
		}
    }

	public function login()
	{
		$condition['account'] = I('post.account', null, 'string');
		$recg = I('post.captcha');
		$condition['password'] = md5(I('post.password', null, 'string'));
		$condition['valid'] = 1;
		//$condition['password'] = md5($_POST['password']);
		$real = session('captcha');
		if ($recg != $real)
		{
			$code = 0;
			$msg = "验证码错误";
			$audit['result'] = '失败';
			$audit['descr'] = '验证码错误';
		}else{
			$user=M('user')->where($condition)->find();
			
			if ($user) {
				$status = $user['status_id'];
				switch ($status) {
					case 1://新注册用户未审核
						$code = 0;
						$msg = '请等待审核通过再登陆';
						$audit['result'] = '失败';
						$audit['descr'] = '未审核';
						break;
					case 2://正常用户
						session('logged', true);
						session('username', $condition['account']);
						session('role_num',$user['role_id']);
						$audit['result'] = '成功';
						$code = 1;
						$msg = '成功';
						break;
					case 4://被锁定用户
						$code = 0;
						$msg = '用户已被锁定';
						$audit['result'] = '失败';
						$audit['descr'] = '用户已被锁定';
						break;
					default:
						$code = 0;
						$msg = '用户状态不明';
						$audit['result'] = '失败';
						$audit['descr'] = '用户状态不明';
						break;
				}

			}else{
	            $audit['result'] = '失败';
	            $audit['descr'] = '用户名不存在或密码错误';
				$code = 0;
				$msg = '用户名不存在或密码错误';
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
		if(session('logged')){
			$audit['name'] = session('username');
			$audit['ip'] = getIp();
			$audit['module'] = '退出登录';
			$audit['time'] = date('y-m-d h:i:s',time());
			$audit['result'] = '成功';
			M('audit')->add($audit);
			session(null);
			session("logged",false);
			header("Cache-control: no-cache, no-store");
		}

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
			$username = I('post.account', null, 'string');
			$password = I('post.pwd', null, 'string');
			$college_name = I('post.college_name',null,'string');
			$real_name = I('post.real_name',null,'string');


			//处理学院信息 college_id
			$college = M("college")->where(array("name"=>$college_name))->find();
			$college_id = $college['id'];
			if($college_id>0){
				$data["college_id"] = $college_id;

				$data['account'] = $username;
				$data['password'] = md5($password);
				$data['real_name'] = $real_name;
				$data['reg_date'] = date('y-m-d h:i:s', time());
				$data['role_id'] = 1;//新注册用户统一为普通用户
				$data['status_id'] = 1;//新注册用户为待审核状态
				$data['valid'] = 1;//新用户有效

				$count = M('user')->where(array('account'=>$username))->count();
				if($count==0){
					M('user')->add($data);
					session('username', $username);
					session('logged','0');
					/*将操作结果写入日志*/
					$audit['descr'] = '新用户注册成功';
					$audit['name'] = session('username');
					$audit['ip'] = getIp();
					$audit['module'] = '用户信息';
					$audit['time'] = date('y-m-d h:i:s', time());
					$audit['descr'] = "$username 成功注册成为新用户，待审核";
					M('audit')->add($audit);
					$this->success('注册成功，请等待管理员审核通过！');
				}else{
					$this->error('存在同名用户，请另选登录名');
				}

			}else{
//				dump($college_name);
				$this->error("部门信息不存在，注册失败！");
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
		session('captcha',$result-$num);
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