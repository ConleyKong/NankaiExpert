<?php namespace Home\Model;
use Think\Model\ViewModel;


class PaperViewModel extends ViewModel {
	public $viewFields = array(
		'paper' => array(
			'id',
			'first_author_id' => 'firstauthor_id',
			'name',
			'publish_date',
			'paper_type',
			'conference_name',
			'other_author',
			'valid',
            '_type' => 'LEFT'
		),
		'person' => array(
			'id' => 'author_id',
			'name' => 'person_name',
			'_on' => 'paper.first_author_id=person.id',
            '_type' => 'LEFT'
		),
		'college' => array(
			'id' => 'col_id',
			'name' => 'col_name',
			'_on' => 'paper.college_id=college.id',
			 '_type' => 'LEFT'
		)
	);
}