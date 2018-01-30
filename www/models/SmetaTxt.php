<?php

namespace app\models;

use yii\db\ActiveRecord;

class SmetaTxt extends ActiveRecord {

    public static function tableName()
    {
        return '{{smetatxt}}';
    }
	
	public function attributeLabels()
    {
        return [
			'id_client'=>'Номер клиента',
			'ew'=>'Тип работ',
			'smeta_id'=>'Номер сметы',
			'poz' =>'Поз',
			'name'=>'Наименование',
			'kol'=>'Количество',
			'ed'=>'Ед.изм.',
			'price'=>'Стоимость',
			'summa'=>'Сумма'
        ];
    }	
		
	
	
	
	public function rules()
    {
        return [
            [['id_client', 'ew', 'smeta_id','name','kol','price','summa'], 'required'],
			[['ew', 'name','ed'], 'string'],
			[['kol','price','summa'],'double'],
			[['id_client','smeta_id','poz'],'integer']		
        ];
    }
		
	
}

?>