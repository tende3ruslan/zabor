<?php

namespace app\models;

use yii\db\ActiveRecord;
class Element extends ActiveRecord {

    public static function tableName()
    {
        return '{{element}}';
    }
	
	
}

?>