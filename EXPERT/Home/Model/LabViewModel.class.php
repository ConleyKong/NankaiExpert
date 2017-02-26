<?php namespace Home\Model;
use Think\Model\ViewModel;

class LabViewModel extends ViewModel {
	public $viewFields = array(
		'lab'	=> 	array(
			'id',
			'name',
			'location',
			'formed_year',
			'college_id',
			'members',
			'description',
			'research_interests',
			'research_results',
			'lab_type',
			'valid',
		),
		'college' => array(
			'name' => 'college_name',
			'_on' => 'lab.college_id=college.id',
			'_type' => 'LEFT',
		),
	);
}

