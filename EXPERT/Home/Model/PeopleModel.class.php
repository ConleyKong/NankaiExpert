<?php namespace Home\Model;
use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Conley.K
 * Date: 2016/8/27 0027
 * Time: 19:36
 * Desc: 专家个人信息操作
 */

Class PeopleModel extends RelationModel{

    //定义主表名称
    Protected $tableName = 'person';

    //定义用户与用户信处表关联关系属性
    protected $_link = array(
        'degree'=>array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'person_degree',
            //外键，也就是表person中的字段
            'foreign_key'   => 'degree_id',
            //关联的字段，可以多个
            'mapping_fields'=>'name',

            'as_fields'=>'name:degree_name'
        ),
        );
}