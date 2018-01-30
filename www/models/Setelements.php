<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Setelements extends ActiveRecord {
	public $inputdata=array();
	public $Id;
	public $price;

public static function TableName() {
	return 'element_type';
}

    public function attributeLabels()
    {
        return [
			'Id'=>'id',
            'price' => 'price'
        ];
    }

 public function rules()
    {

    return    [
	[['price'],'double']
	];
	
    }
	

    public function readAll()

    {
		$typeNames = Element_Type::find()->asArray()->all();
		
		$data=array();
		foreach ($typeNames as $var) {
			$data['Id']=$var['Id'];
			$data['name']   =$var['name'];
			$this->inputdata[$var['Id']]['Id']=$var['Id'];
			$this->inputdata[$var['Id']]['price']=$var['price'];
		}
		return $data;
    }
	/*
	public function validate() {
		
		return true;	
	}
	*/
	
	
	public function loadData() {
		
		$typeNames = Element_Name::find()->asArray()->all();
		$element_types = Element_Type::find()->asArray()->all();
		$ret=['elementNames'=>$elementNames,'element_types'=>$element_types];
		return $true;
		
	}	
	
	public function saveData() {
		
	}
	
}

?>