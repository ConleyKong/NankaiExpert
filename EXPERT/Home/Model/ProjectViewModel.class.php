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
			'fund',
			'financial_account',
			'project_no',
			'source_department',
		),
		'person' => array(
			'name' => 'manager_name',
			'_on' => 'project.manager_id=person.id'
		),
		'college' => array(
			'name' => 'college_name',
			'_on' => 'project.college_id=college.id'
		),
		'project_type' => array(
			'name' => 'type_name',
			'_on' => 'project_type.id=project.type_id',
		)
	);
}

