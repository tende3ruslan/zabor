<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class listWorkNames extends ActiveRecord {

 public static function TableName() {
		return 'rabota_name';
}


 public function attributeLabels()  {
        return [
			'type_id'=>'type_id',
            'name' => 'имя'
        ];
    }

	
 public function rules()   {
    return 
	[
        [['type_id'], 'integer'],
		[['name'], 'string'],
	];
    }
	



	
}

?>