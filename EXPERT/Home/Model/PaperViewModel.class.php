<?php namespace Home\Model;
use Think\Model\ViewModel;


class PaperViewModel extends ViewModel {
	public $viewFields = array(
		'paper' => array(
			'id',
			'name',
			'first_author_id' => 'firstauthor_id',
			'first_author',
			'other_authors_name',
			'contact_author_id',
			'contact_author',
			'english_authors',
			'publish_year',
			'paper_type',
			'conference_name',
			'article_type',
			'isbn',
			'issue',
			'page_range',
			'college_id',
			'comment',
			'valid',
            '_type' => 'LEFT'
		),
		'person' => array(
			'id' => 'author_id',
			'title_id',
			'name' => 'person_name',
			'_on' => 'paper.first_author_id=person.id',
            '_type' => 'LEFT'
		),
		'college' => array(
			'id' => 'college_id',
			'name' => 'col_name',
			'_on' => 'paper.college_id=college.id',
			 '_type' => 'LEFT'
		),
		'contacter' => array(
			'_table' => 'person',
			'id' =>'contactor_id',
			'name' =>'contacter_name',
			'_on' => 'paper.contact_author_id=contacter.id',
			'_type' => 'left',
		),
//		'type' => array(
//			'_table' => 'paper_type',
//			'id' => 'paper_type_id',
//			'name' => 'type_name',
//			'_on' => 'paper.type_id=type.id',
//		),
	);
}