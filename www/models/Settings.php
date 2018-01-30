<?php

namespace app\models;

use yii\db\ActiveRecord;

class Settings extends ActiveRecord {

    public static function tableName()
    {
        return '{{settings}}';
    }
	
	public function attributeLabels()
    {
        return [
			'name'=>'Название параметра',
			'attr'=>'Параметр',
			'value'=>'Значение'
        ];
    }	
		
	
	public function rules()
    {
        return [
            [['name', 'attr', 'value'], 'required'],
			[['name','attr'], 'string'],
			[['value'],'double']	
        ];
    }
		
	
}

?>