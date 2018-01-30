<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class CalcFundament  extends FuncZabor {
	
	const STEP1='step1';
	public $calc_step;
	
	public $len;							//длина фундамента
	public $height;						//высота фундамента
	public $width;						//ширина фундамента
	public $width_text;				
	public $zaglublenie;				//заглубление фундамента
	public $nad;							//над  землей
	public $armokarkas;			//армокаркас под бетонирование
	public $armokarkas_text;	
	public $stolb_razmer;			//размер столба
	public $stolb_razmer_text;	
	public $stolb_count;				//количество столбов	
	public $len_stolb;					//длина столбов
	public $stolb_grunt;				//грунтовка столбов
	public $stolb_okraska;			//окраска столбов
	public $stolb_grunt_text;		//грунтовка столбов
	public $stolb_okraska_text;	//окраска столбов	
	
	public $focus_input;

    public static function tableName()
    {
        return '{{element_type}}';
    }
	

	
	public function attributeLabels()
    {
        return [
			'len'=>'Длина фундамента',
			'height'=>'Высота фундамента',
			'width'=>'Ширина фундамента',	
			'zaglublenie'=>'Заглубление',
			'nad'=>'Над землей',
			'armokarkas'=>'Армокаркас',
			'stolb_count'=>'Количество столбов',
			'stolb_razmer'=>'Размер столба',
			'len_stolb'=>'Длина столбов',
			'stolb_grunt'=>'Грунтовка столбов',
			'stolb_okraska'=>'Окраска столов'
        ];
    }
	
	
	
	public function scenarios()
  {
      $scenarios = parent::scenarios();
		$scenarios['step1'] = ['len', 'height', 'width', 'zaglublenie', 'nad', 'armokarkas', 'stolb_count', 'stolb_razmer','len_stolb','stolb_grunt', 'stolb_okraska','focus_input'];
        return $scenarios;
  }
  
  
    public function rules()
    {
        return 
		[
			[['stolb_count'], 'integer'],
			[[ 'width','armokarkas','stolb_razmer','stolb_grunt', 'stolb_okraska', 'focus_input'], 'string'],
			[['len', 'height', 'zaglublenie', 'nad','len_stolb'], 'double'],
			[['len', 'height','zaglublenie', 'nad'], 'required','on'=>'step1']
			//'len', 'height', 'width', 'zaglublenie', 'nad', 'armokarkas', 'stolb_count', 'razmer_stolb','len_stolb'
        ];
    }

	
	
	
	
	public function calcInit1() { // устанавливаем значения при первом открытии формы
		$this->height=0.4;
		$this->zaglublenie=0.3;
		$this->width_text=$this->readElements(5000,1);	
		$this->armokarkas_text=$this->readElements(5000,2);			
		$this->stolb_razmer_text=$this->readElements(1,0);	
		$this->nad=$this->height-$this->zaglublenie;
		$this->stolb_grunt_text=$this->readElements(9,0);							// грунтуем
		$this->stolb_okraska_text=$this->readElements(12,1);					// окрашиваем		
		//если выбрано покрытие 3 в 1  то выбор краски меняем на выбор цвета
		if ($this->stolb_grunt=='pokritie_3v1') {
			$this->stolb_okraska_text=$this->readElements(10,1)+$this->readElements(10,2);				
		}		
		return;
	}
	
	public function calcStep1() { 	// расчет после изменения любого из значений формы
		$this->width_text=$this->readElements(5000,1);		
		$this->armokarkas_text=$this->readElements(5000,2);		
		$this->stolb_razmer_text=$this->readElements(1,0);
		$this->nad=$this->height-$this->zaglublenie;
		$this->stolb_grunt_text=$this->readElements(9,0);							// грунтуем
		$this->stolb_okraska_text=$this->readElements(12,1);					// окрашиваем		
		//если выбрано покрытие 3 в 1  то выбор краски меняем на выбор цвета
		if ($this->stolb_grunt=='pokritie_3v1') {
			$this->stolb_okraska_text=$this->readElements(10,1)+$this->readElements(10,2);				
		}											
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
				$inputdata->type_calc="fundament";
				$inputdata->user_id=$c;
				$inputdata->smeta_id=$s;
				$inputdata->input_attr= $i;
				$inputdata->input_label=$item;
				$inputdata->input_data=$this->$i;
				$inputdata->datetime=date("Y-m-d H:i:s");
				$inputdata->save(false);
			}
	
			
			// ******************************************************************************
			
			//***************************  СОХРАНЯЕМ СМЕТУ   ***********************
				
				
				
				// ########  сохраняем тип : фундамент
				
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='fundament';
					$smeta->attr   = 'fundament'; 
					$smeta->type  = $s; 
					$smeta->kol  = 0;
					$smeta->ed='';
					$smeta->price =  0;
					$smeta->table = 'info';	//признак информационной записи
					$smeta->insert(false); 

				// END ######## 



				
				// ########### материалы на фундамент

					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='fundament';
					
					$smeta->attr   = $this->width.'_400'; 						// attr : fundament_250 to fundament_250_400
					$smeta->type  = '( Длина фундамента='.$this->len.'м, заглуб. '.$this->zaglublenie.' м )'; 
					$smeta->kol    = $this->len;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e1';	//признак материалов
					$smeta->insert(false); 	
		
				// END ########### материалы на фундмент

				// ########### работы  на фундамент

					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='fundament';
					
					$smeta->attr   = $this->width.'_400'; 						// attr : fundament_250 to fundament_250_400
					$smeta->type  = '( Длина фундамента '.$this->len.'м )'; 
					$smeta->kol    = $this->len;
					$smeta->ed='м';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w1';	//признак материалов
					$smeta->insert(false); 	
		
				// END ########### материалы на фундмент
				
					
				
				
				
			   // ########### столбы в смету	
			   
			   if ($this->stolb_count>0) {
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='fundament';
				
				$smeta->attr   =$this->stolb_razmer;
				
				
				$smeta->type  = '( Длина столба '.$this->len_stolb.' м, '.$this->getelement_name($this->stolb_grunt).',  '.$this->getelement_name($this->stolb_okraska).')';
				$smeta->kol    = $this->stolb_count;
				$smeta->ed='шт.';
				// стоимость столба это цена за метр умноженные на высоту столба
				$smeta->price =  $this->getelement_price($this->stolb_razmer)*$this->len_stolb;
				
				$dop='';
				
				//добавляем в цену столба стоимость грунтовки
				if ($this->stolb_grunt != 'grunt_none') {
					$smeta->price = $smeta->price + $this->getelement_price($this->stolb_grunt)*$this->len_stolb;
				}
				//добавляем в цену столба стоимость покраски
				if ($this->stolb_okraska != 'kraska_none') {
					$smeta->price = $smeta->price + $this->getelement_price($this->stolb_okraska)*$this->len_stolb;
				}				
				
				$smeta->table = 'e2';	//признак материалов. признак dop учитывается для установки по умолчанию полей например калитки
				
				$smeta->insert(false); 				
				// END ########### столбы в смету
				
				
				if ($this->armokarkas=='armokarkas_da') {
				// ########### материалы на фундамент

					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='fundament';
					
					$smeta->attr   = 'fundament_armokarkas_do_1_5'; 		
					$smeta->type  = ''; 
					$smeta->kol    =$this->stolb_count;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e3';	//признак материалов
					$smeta->insert(false); 	
		
				// END ########### материалы на фундмент

				// ########### работы  на фундамент

					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='fundament';
					
					$smeta->attr   = 'fundament_armokarkas_do_1_5';
					$smeta->type  = ''; 
					$smeta->kol    = $this->stolb_count;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w3';	
					$smeta->insert(false); 	
		
				// END ########### материалы на фундмент					
					
				} //конец если есть армокаркас
				
				// ############# крышек в смету
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='fundament';
			
				
				$smeta->attr = ( $this->stolb_razmer=='stolb_60_60') ? 'krishka_stolb_60_60' : 'krishka_stolb_80_80';
				
				$smeta->type  ='';// 'Размер '.( $this->stolb_razmer=='stolb_60_60') ? '60х60' : '80х80';
				$smeta->kol    = $this->stolb_count;
				$smeta->ed='шт.';
				$smeta->price =  $this->getelement_price($smeta->attr);
				$smeta->table = 'e4';	//признак материалов
				$smeta->insert(false); 			

				// END ############# крышек в смету				
			  
			 } // конец если есть столбы 
			   
			//************************ КОНЕЦ СОХРАНЯЕМ СМЕТУ  *********************	
			
				$smeta->readSmeta($c,$s);

				
			} else {
			
					$this->calc_step=1;
					$this->calcStep1();
			}	
	
	
	
	
}	
	
	
	
}

?>