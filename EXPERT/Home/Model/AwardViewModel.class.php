<?php namespace Home\Model;
use Think\Model\ViewModel;


class AwardViewModel extends ViewModel {
	public $viewFields = array(
		'award'	=> 	array(
			'id',
			'first_id',
			'name',
			'level',
			'achievement',
			'date' => 'time'
		),
		'person' => array(
			'id' => 'person_id',
			'name' => 'person_name',
			'birthday',
			'_on' => 'person.id=award.first_id',
			'_type' => 'LEFT',
		),
		'person_title' => array(
			'id' => 'grade_id',
			'name' => 'grade_name',
			'_on' => 'person.title_id=person_title.id',
			'_type' => 'LEFT',
		),
		'person_honor' => array(
			'_on' => 'person.id=person_honor.person_id',
			'_type' => 'LEFT',
		),
		'academic_honor' => array(
			'id' => 'academichonor_id',
			'name' => 'academichonor_name',
			'_on' => 'person_honor.honor_id=academic_honor.id',
		),
		'college' => array(
			'id' => 'college_id',
			'name' => 'col_name',
			'_on' => 'college.id=person.college_id',
		),
	);
}