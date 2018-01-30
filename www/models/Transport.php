<?php

namespace app\models;

use yii\db\ActiveRecord;
class Transport extends ActiveRecord {

    public static function tableName()
    {
        return '{{transport}}';
    }
	
	
}

?>