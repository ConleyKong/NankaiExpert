<?php namespace Home\Model;
use Think\Model\ViewModel;

class InterChangesViewModel extends ViewModel {
	public $viewFields = array(
		'ic'	=> 	array(
			'_table' => 'interchanges',
			'id',
			'name',
			'location',
			'record_date',
			'college_id',
			'person_name',
			'person_id',
			'comment',
			'type_id',
			'valid',
		),
		'person' => array(
			'gender',
			'employee_no',
			'name' => 'expert_name',
			'title_id',
			'_on' => 'person.id=ic.person_id',
			'_type' => 'LEFT'
		),
		'person_title' => array(
			'name' => 'title_name',
			'_on' => 'person.title_id=person_title.id',
			'_type' => 'LEFT'
		),
		'college' => array(
			'name' => 'college_name',
//			'name' => 'join_unit',
			'_on' => 'college.id=ic.college_id',
			'_type' => 'LEFT'
		),
		'ic_type' => array(
			'name' => 'type_name',
			'_on' => 'ic_type.id=ic.type_id',
			'_type' => 'LEFT'
		)
	);
}

