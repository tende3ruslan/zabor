<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class CalcParkovka  extends FuncZabor {
	
	const STEP1='step1';
	public $calc_step;
	
	public $height;						// высота калитки
	public $len;							//ширина калитки
	public $width;
	public $width_text;
	public $focus_input;		

    public static function tableName()
    {
        return '{{element_type}}';
    }
	

	
	public function attributeLabels()
    {
        return [
			'len'=>'Длина',
			'height'=>'Ширина',
			'width'=>'Толщина',	
        ];
    }
	
	
	
	public function scenarios()
  {
      $scenarios = parent::scenarios();
		$scenarios['step1'] = ['len','height','width', 'focus_input'];
        return $scenarios;
  }
  
  
    public function rules()
    {
        return 
		[
			[['len','height','width'], 'double'],
			[[], 'integer'],
			[['len','height','width'], 'required','on'=>'step1']
        ];
    }

	
	
	
	
	public function calcInit1() { // устанавливаем значения при первом открытии формы
		$this->width_text=$this->readElements(6500,2);	
		return;
	}
	
	public function calcStep1() { 	// расчет после изменения любого из значений формы
		$this->width_text=$this->readElements(6500,2);										
		return;
	}
	
	/*
	
	public function getwork($attr) {
		$element = Rabota_Type::find()->select(['id','name', 'kol'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['kol'];
	}
	
	public function getelement($attr) {
		$element = Element_Type::find()->select(['id','name', 'kol'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['kol'];
	}

	
	public function getelement_price($attr) {
		$element = Element_Type::find()->select(['price'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['price'];	
	}
	
	public function getwork_price($attr) {
		$element = Rabota_Type::find()->select(['price'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['price'];	
	}
	
	public function getelement_name($attr) {
		$element = Element_Type::find()->select(['name'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['name'];	
	}
	
	public function getwork_name($attr) {
		$element = Rabota_Type::find()->select(['name'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['name'];	
	}
	
    public function readElements($type,$subtype)

    {
		if ($subtype==0) {
		 $element = Element_Type::find()->select(['attr','name'])->where(['type' => $type])->orderBy('id')->asArray()->all();
		} else {
		 $element = Element_Type::find()->select(['attr','name'])->where(['type' => $type,'sub_type' => $subtype])->orderBy('id')->asArray()->all();	
		}
		$arr=array();
		foreach ($element as $item) {
			
			$arr[$item['attr']]=$item['name'];
			
		}
		return $arr;
    }
	
	 public function readWorks($type)

    {
		$element = Rabota_Type::find()->select(['attr','name'])->where(['type' => $type])->orderBy('id')->asArray()->all();
		$arr=array();
		foreach ($element as $item) {
			
			$arr[$item['attr']]=$item['name'];
			
		}
		return $arr;
    }
	
*/
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
				$inputdata->type_calc="parkovka";
				$inputdata->user_id=$c;
				$inputdata->smeta_id=$s;
				$inputdata->input_attr= $i;
				$inputdata->input_label=$item;
				$inputdata->input_data=$this->$i;
				$inputdata->datetime=date("Y-m-d H:i:s");
				$inputdata->save(false);
			}
	
			
			// ******************************************************************************
			
			//***************************  СОХРАНЯЕМ СМЕТУ НА ПРОФЛИСТ  ***********************
				
				
				
				// ########  сохраняем тип : фундамент
				
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='parkovka';
					$smeta->attr   = 'parkovka'; 
					$smeta->type  = $s; 
					$smeta->kol  = 0;
					$smeta->ed='';
					$smeta->price =  0;
					$smeta->table = 'info';	//признак информационной записи
					$smeta->insert(false); 

				// END ######## 



				
				// ########### материалы на парковочное место

					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='parkovka';
					
					$smeta->attr   = 'parkovka_'.$this->width; 						
					$smeta->type  = '( песок, щебень, бетон, сетка дорожная ) длина '.$this->len.'м, ширина '.$this->height.'м'; 
					$smeta->kol    = $this->len*$this->height;
					$smeta->ed='кв.м.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e1';	//признак материалов
					$smeta->insert(false); 	
		
				// END ########### материалы на парковочное место

				// ########### работы на парковочное место

					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='parkovka';
					
					$smeta->attr   = 'parkovka_'.$this->width; 						
					$smeta->type  = '(площадь '.$this->len*$this->height.' кв.м.)'; 
					$smeta->kol    = $this->len*$this->height;
					$smeta->ed='кв. м.';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w1';	//признак материалов
					$smeta->insert(false); 	
		
				// END ########### работы на парковочное место
				
			//************************ КОНЕЦ СОХРАНЯЕМ СМЕТУ НА ПРОФЛИСТ *********************	
			
				$smeta->readSmeta($c,$s);

				
			} else {
			
					$this->calc_step=1;
					$this->calcStep1();
			}	
	
	
	
	
	}		

	
	
}

?>