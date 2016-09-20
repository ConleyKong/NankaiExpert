<?php
namespace Home\Controller;
use Think\Controller;
class HomepageController extends Controller {
	function index(){
		if (! session('logged'))
		{
			$this->redirect('Index/index');
		}
		else 
		{
			$this->display();
		}
	}
}