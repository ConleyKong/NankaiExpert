<?php namespace Home\Model;
use Think\Model\ViewModel;
/**
 * Created by PhpStorm.
 * User: Conley.K
 * Date: 2017/4/5 0005
 * Time: 23:16
 * Describe: Used for statistics count for person's paper
 */

class PersonPaperViewModel extends ViewModel {
    public $viewFields = array(
        'person'	=> 	array(
            'id'=>'person_id',
            'employee_no',
            'name'=>'person_name',
            'gender',
            'college_names',
            'title_id',
            'valid'=>'person_valid',
            'COUNT(paper.id)'=>'primary_sum',
//            'COUNT(second_paper.id)'=>'contact_sum1',
//            'COUNT(third_paper.id)'=>'contact_sum2',
//            'COUNT(paper_type)'=>'primary_type_sum',
//            'COUNT(second_type)'=>'second_type_sum',
//            'COUNT(third_type)'=>'third_type_sum',
            '_type' => 'LEFT'
        ),
        'person_title'=>array(
            'name'=>'title_name',
            '_on' => 'person_title.id = person.title_id',
            '_type'=>'LEFT'
        ),
         'paper' => array(
            'name'=>'paper_name',
            'paper_type',
            'publish_year',
            'valid'=>'paper_valid',
            '_on'=>'paper.first_author_id = person.id',
            '_type' => 'LEFT'
        ),
        'second_paper'=>array(
            'name'=>'second_name',
            'paper_type'=>'second_type',
            'publish_year'=>'second_publish_year',
            'valid'=>'second_valid',
            '_table'=>'paper',
            '_on'=>'second_paper.contact_author_id=person.id',
            '_type' => 'LEFT'
        ),
        'third_paper'=>array(
            'name'=>'third_name',
            'paper_type'=>'third_type',
            'publish_year'=>'third_publish_year',
            'valid'=>'third_valid',
            '_table'=>'paper',
            '_on'=>'third_paper.second_contact_id=person.id',
            '_type' => 'LEFT'
        )


//
//        'other_author' => array(
//            'title_id'=>'others_title_id',
//            'name'=>'others_name',
//            '_table'=>'person',
//            '_on'=>'other_author.id=paper_other_authors.person_id',
//            '_type'=>'LEFT'
//        )
//        'person' => array(
//            'id' => 'author_id',
//            'title_id',
//            'name' => 'person_name',
//            '_on' => 'paper.first_author_id=person.id',
//            '_type' => 'LEFT'
//        ),
//        'contacter' => array(
//            'name' =>'contacter_name',
//            'title_id'=>'contacter_title_id',
//            '_table' => 'person',
//            'id' =>'contactor_id',
//            '_on' => 'paper.contact_author_id=contacter.id',
//            '_type' => 'left',
//        ),

//		'type' => array(
//			'_table' => 'paper_type',
//			'id' => 'paper_type_id',
//			'name' => 'type_name',
//			'_on' => 'paper.type_id=type.id',
//		),
    );
}