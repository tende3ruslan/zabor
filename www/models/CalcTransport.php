<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class CalcTransport  extends FuncZabor {
	
	const STEP1='step1';
	public $calc_step;
	
	public $len;										//расстояние
	public $ves;
	public $ves_text;								//вес
	public $reisov;
	public $focus_input;

    public static function tableName()
    {
        return '{{element_type}}';
    }
	

	
	public function attributeLabels()
    {
        return [
			'len'=>'Расстояние, км',
			'ves'=>'Вес',
			'reisov'=>'Рейсов'

        ];
    }
	
	
	
	public function scenarios()
  {
      $scenarios = parent::scenarios();
		$scenarios['step1'] = ['len','ves','reisov', 'focus_input'];
        return $scenarios;
  }
  
  
    public function rules()
    {
        return 
		[
			[['len','ves','reisov'], 'integer'],
			[['len','ves','reisov'], 'required','on'=>'step1']
        ];
    }

	
	
	
	
	public function calcInit1() { // устанавливаем значения при первом открытии формы
		$this->ves_text=['1500'=>'До 1.5 тонн','5000'=>'От 1.5 до 5 тонн','10000'=>'Свыше 5 тонн'];
		$this->reisov=1;
		return;
	}
	
	public function calcStep1() { 	// расчет после изменения любого из значений формы
		$this->ves_text=['1500'=>'До 1.5 тонн','5000'=>'От 1.5 до 5 тонн','10000'=>'Свыше 5 тонн'];									
		return;
	}
	

		public function calc ($c,$s,$smeta) {
	
						
			//Yii::warning('загрузили пост данные в модель');
			
			if ($this->focus_input=='clear') {  // если нажата кнопка "очистить  результаты"
			 $this->calcInit1();
			}
			
			if ($this->focus_input=='smeta') {  // если нажата кнопка смета
			
				$this->calc_step=1;
				$this->calcStep1();
			
			// **************************   СОХРАНЯЕМ ФОРМУ В ТАБЛИЦУ      ***********************
			
			//  [['type_calc' ,'user_id', 'smeta_id','input_attr','input_label','input_data','datetime'] - поля таблицы для заполнения
			$inputdata=new InputForm();				//чистим базу, поправить потом
			$smeta->delete_smeta($c,$s);				//чистим таблицу сметы для данного расчета
			$inputdata->delete_inputform($c,$s);  //чистим таблицу формы для данного расчета
			
			$col=$this->attributeLabels(); // получаем названия полей формы 
	
			foreach ($col as $i=>$item) {
				$inputdata=new InputForm();
				$inputdata->type_calc="svai";
				$inputdata->user_id=$c;
				$inputdata->smeta_id=$s;
				$inputdata->input_attr= $i;
				$inputdata->input_label=$item;
				$inputdata->input_data=$this->$i;
				$inputdata->datetime=date("Y-m-d H:i:s");
				$inputdata->save(false);
			}
	
			
			// ******************************************************************************
			
			//***************************  СОХРАНЯЕМ СМЕТУ НА ТРАНСПОРТ  ***********************
				
				
				
				// ########  сохраняем тип : транспорт
				
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='transport';
					$smeta->attr   = 'transport'; 
					$smeta->type  = $s; 
					$smeta->kol  = 0;
					$smeta->ed='';
					$smeta->price =  0;
					$smeta->table = 'info';	//признак информационной записи
					$smeta->insert(false); 

				// END ######## 


				 $baseradius=$this->getelement_price('baseradius'); //базовое расстояние
				 $dopradius=$this->len-$baseradius;
				
				// ########### транспортные расходы							
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='transport';
					
					$smeta->attr   = 'ves_'.$this->ves.'_forbaseradius'; 						
					$smeta->type  = '/ Рейсов: '.$this->reisov; 
					$smeta->kol    = $this->len*$this->reisov;
					$smeta->ed='км';
					$smeta->price = ($dopradius>0) ? (($this->getelement_price($smeta->attr)+$dopradius*$this->getelement_price('ves_'.$this->ves.'_zakm'))/$this->len): ($this->getelement_price($smeta->attr)/$this->len);
					$smeta->table = 'e1';	//признак материалов
					$smeta->insert(false); 	
		
				// END ########### транспортные расходы
				
				
			//************************ КОНЕЦ СОХРАНЯЕМ СМЕТУ НА ПРОФЛИСТ *********************	
			
				$smeta->readSmeta($c,$s);

				
			} else {
			
					$this->calc_step=1;
					$this->calcStep1();
			}	
	
	
	
	
	}			
	
	
	
}

?>