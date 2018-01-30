<?php

namespace app\models;

use yii\db\ActiveRecord;
class Rabota_Name extends ActiveRecord {

    public static function tableName()
    {
        return '{{rabota_name}}';
    }
	
	
    public function readAll()

    {

		$rabota_names = Rabota_name::find()->orderBy('type_id')->asArray()->all();
		
		return $rabota_names;
    }
}

?>