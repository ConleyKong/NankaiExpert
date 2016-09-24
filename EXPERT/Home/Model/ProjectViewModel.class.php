<?php namespace Home\Model;
use Think\Model\ViewModel;

class ProjectViewModel extends ViewModel {
	public $viewFields = array(
		'project'	=> 	array(
			'id',
			'name',
			'type_id',
			'support_no',
			'manager_id',
			'source',
			'start_time',
			'end_time',
			'college_id',
			'direct_fund',
			'indirect_fund',
			'fund',
			'financial_account',
			'project_no',
			'source_department',
			'comment',
			'valid',
		),
		'person' => array(
			'name' => 'person_name',
			'_on' => 'person.id=project.manager_id',
			'_type' => 'LEFT'
		),
		'college' => array(
//			'name' => 'college_name',
			'name' => 'join_unit',
			'_on' => 'college.id=project.college_id',
			'_type' => 'LEFT'
		),
		'project_type' => array(
			'name' => 'type_name',
			'_on' => 'project_type.id=project.type_id',
			'_type' => 'LEFT'
		)
	);
}

