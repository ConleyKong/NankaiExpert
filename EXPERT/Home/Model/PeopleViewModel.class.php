<?php namespace Home\Model;
use Think\Model\ViewModel;

class PeopleViewModel extends ViewModel {
	public $viewFields = array(
		'person'	=> 	array(
			'id',
			'name',
			'gender',
			'employee_no',
			'college_id',
			'postdoctor',
			'title_id',
			'birthday',
			'email',
			'phone',
			'first_class',
			'second_class',
			'honor_records',
			'group_name',
			'group_id',
			'credit',
			'party',
			'degree_id',
			'mentor_type_id',
			'_type' => 'LEFT'
		),
		'college' => array(
//			'id' => 'college_id',
			'name' => 'college_name',
			'_on' => 'person.college_id=college.id',
			'_type' => 'LEFT'
		),
		'person_mentor_type' => array(
			'id' => 'men_id',
			'name' => 'mentor_name',
			'_on' => 'person.mentor_type_id=person_mentor_type.id',
			'_type' => 'LEFT'
		),
		'person_degree' => array(
			'id' => 'deg_id',
			'name' => 'degree_name',
			'_on' => 'person.degree_id=person_degree.id',
			'_type' => 'LEFT'
		),
		'person_title' => array(
			'id' => 'gra_id',
			'name' => 'title_name',
			'_on' => 'person.title_id=person_title.id',
			'_type' => 'LEFT'
		),
		'person_type' => array(
//			'id' => 'per_id',
			'name' => 'person_type',
			'_on' => 'person.type_id=person_type.id',
			'_type' => 'LEFT'
		),
		'person_honor' => array(
			'_on' => 'person.id=person_honor.person_id',
			'_type' => 'LEFT'
		),
//		'academic_honor' => array(
//			'id' => 'academichonor_id',
//			'name' => 'academic_name',
//			'abbr_name'=>'academic_abbr_name',
//			'_on' => 'academic_honor.id=person_honor.honor_id',
//		),

	);
}