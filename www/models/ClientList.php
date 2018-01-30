<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\GridView;

class Client extends ActiveRecord {
	public $focus_input;
	public $work_status_text=['1'=>'расчет','2'=>'думает','3'=>'подписан договор', '4'=>'работы начаты', '5'=>'работы завершены'];	

    public static function tableName()
    {
        return '{{clients}}';
    }
	
	
public function attributeLabels()
    {
        return [
		
			'id_client'=>'Клиент', 
			'dogovor'=>'Договор',
			'name'=> 'Имя',
			'tel'=>'Тел', 
			'address'=>'Адрес',
			'passport'=>'Паспортные данные',
			'email'=>'E-Mail',
			'w'=>'Общая длина забора',		
			'h'=>'Высота забора',
			'comment'=>'Комментарий',
			'datetime'=>'Дата',
			'summa'=>'Итоговая сумма',
			'datestart'=>'Дата начала',
			'dateend'=>'Дата завершения',
			'avans'=>'Сумма аванса',
			'work_status'=>'Статус работ'
        ];
    }
	
		
	
	public function rules()
    {
        return [
			[['id_client'], 'integer'],
			[['h', 'w','summa','avans'], 'double'],
            [['focus_input','dogovor','name','tel', 'address', 'email','comment','arhiv','passport','datestart','dateend','work_status'], 'string',]
        ];
    }
	
	

	
}	

?>