<?php

namespace app\models;

use yii\db\ActiveRecord;
class Parkovka extends ActiveRecord {

    public static function tableName()
    {
        return '{{parkovka}}';
    }
	
	
}

?>