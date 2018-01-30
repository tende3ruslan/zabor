<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;
use app\models\SmetaTxt;

class EditSmeta extends FuncZabor {

	public $usermail;
	public $document;
	public $add_element;							//добавочный коэффициент на материалы
	public $add_work;								//добавочный коэффициент на работы

	public $focus_input;


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
			'summa'=>'Сумма',
			'usermail'=>'Email для сметы',
			'add_element'=>'Материалы х k=',
			'add_work'=>'Работы х k='
        ];
    }	
		
	
	
	
	public function rules()
    {
        return [
            [['name','kol','price','summa','usermail'], 'required'],
			[['ew', 'name','ed','focus_input'], 'string'],
			[['kol','price','summa','add_element','add_work'],'double'],
			[['id_client','smeta_id','poz'],'integer'],		
			[['usermail'],'email']
        ];
    }	
	
	
	
	
	


	
}	

?>