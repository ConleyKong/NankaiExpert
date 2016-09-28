<?php namespace Home\Model;
use Think\Model\ViewModel;

class UserViewModel extends ViewModel {
	public $viewFields = array(
		'user'	=> 	array(
			'id',
			'account',
			'password',
			'real_name',
			'college_id',
			'role_id',
			'valid',
			'reg_date',
			'_type' => 'LEFT'
		),
		'role_type' => array(
			'id'=>'roleTypeId',
			'role_name',
			'_on' => 'role_type.id = role_id',
			'_type' => 'LEFT'
		),
		'college' => array(
			'id'=>'collegeId',
			'name' => 'college_name',
			'_on' => 'college.id = college_id',
			'_type'=>'LEFT',
		),
	);
}