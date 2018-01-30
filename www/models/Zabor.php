<?php

namespace app\models;

use yii\db\ActiveRecord;

class Zabor extends ActiveRecord {


    public static function tableName()
    {
        return '{{zabor}}';
    }
	

	public function attributeLabels()
    {
        return [
			'id_client'=>'Идентификатор клиента', 
			'poz'=>'Номер в списке работ',
			'h'=>'Высота',
			'l'=> 'Ширина',
			'type'=>'Тип работ', 
			'smeta_id'=>'Номер сметы',
			'input_form_id'=>'Номер расчета',
			'summa'=>'Сумма работ',
			'arhiv'=>'Этап работ'
        ];
    }
	
	
				
	
	
	
	public function rules()
    {
        return [
			[['id_client','poz', 'smeta_id','input_form_id'], 'integer'],
			[['h', 'l', 'summa'], 'double'],
			[['type','arhiv'], 'string']
        ];
    }
	
	
	 public function readZabor()

    {
		
		$zabor = Zabor::find()->select(['id_client','poz','h', 'l', 'type','smeta_id','input_form_id','summa','arhiv'])->orderBy('id')->asArray()->all();
		
		return $zabor;
    }
	
	public function delete_client_id($id_client) 
	
	{
		Zabor::deleteAll(['id_client' => $id_client]);
		return ;
	}
	
}	

?>