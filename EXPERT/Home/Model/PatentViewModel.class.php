<?php namespace Home\Model;
use Think\Model\ViewModel;

class PatentViewModel extends ViewModel {
	public $viewFields = array(
		'patent' => array(
			'id',
			'name',
			'first_inventor_id',
			'first_inventor',
			'college_id',
			'apply_no',
			'apply_date',
			'type',
			'all_inventors',
			'grant_date',
			'patent_state',
			'comment',
			'valid',
			'_type' => 'LEFT'
		),
		'inventor' => array(
			'_table' => 'person',
			'id'=>'inventor_id',
			'name' => 'inventor_name',
			'_on' => 'inventor.id=patent.first_inventor_id',
			'_type' => 'LEFT'
		),
		'owner_college' => array(
			'_table' => 'college',
//			'_as' => 'ownercollege',
			'id' =>'owner_college_id',
			'name' => 'owner_college_name',
			'_on' => 'owner_college.id=patent.college_id',
			'_type' => 'LEFT'
		),
	);
}
