<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class listDiskount extends ActiveRecord {

 public static function TableName() {
		return 'diskount_name';
}


 public function attributeLabels()  {
        return [
			'type_id'=>'type_id',
            'name' => '���'
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