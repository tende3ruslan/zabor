<?php

namespace app\models;

use yii\db\ActiveRecord;
class Element_Name extends ActiveRecord {

    public static function tableName()
    {
        return '{{element_name}}';
    }
	
	
    public function readAll()

    {
		$elementNames = Element_Name::find()->orderBy('type_id')->asArray()->all();

		return $elementNames;
    }
	
}

?>