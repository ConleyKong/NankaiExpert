<?php namespace Home\Model;
use Think\Model\ViewModel;

class PatentViewModel extends ViewModel {
	public $viewFields = array(
		'patent' => array(
			'id',
			'first_inventor_id',
			'name',
			'owner_id',
			'type',
			'apply_no',
			'apply_date',
			'grant_date',
			'comment',
			'valid',
			'_type' => 'LEFT'
		),
		'inventor' => array(
			'_table' => 'person',
			'id'=>'inventor_id',
			'name' => 'firstinventor_name',
			'_on' => 'inventor.id=patent.first_inventor_id',
			'_type' => 'LEFT'
		),
		'owner' => array(
			'_table' => 'person',
			'id' =>'ownerid',
			'name' => 'owner_name',
			'_on' => 'owner.id=patent.owner_id',
			'_type' => 'LEFT'
		),
		'inventorcollege' => array(
			'_table' => 'college',
			'id' =>'inventorcollege_id',
			'name' => 'inventorcollege_name',
			'_on' => 'inventorcollege.id=inventor.college_id',
			'_type' => 'LEFT'
		),
		'ownercollege' => array(
			'_table' => 'college',
//			'_as' => 'ownercollege',
			'id' =>'ownercollege_id',
			'name' => 'ownercollege_name',
			'_on' => 'ownercollege.id=owner.college_id',
			'_type' => 'LEFT'
		),
	);
}
