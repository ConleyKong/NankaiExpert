<?php namespace Home\Model;
use Think\Model\ViewModel;

class LabViewModel extends ViewModel {
	public $viewFields = array(
		'lab'	=> 	array(
			'id',
			'name',
			'manager_id',
			'location',
			'formed_date',
			'college_id',
			'member',
			'description',
			'valid',
		),
		'person' => array(
			'name' => 'manager_name',
			'_on' => 'lab.manager_id=person.id',
			'_type' => 'LEFT',
		),
		'college' => array(
			'name' => 'college_name',
			'_on' => 'lab.college_id=college.id',
			'_type' => 'LEFT',
		),
	);
}

