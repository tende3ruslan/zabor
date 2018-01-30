<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class CalcOtkatnieVorota  extends FuncZabor {
	
	const STEP1='step1';
	public $calc_step;
	
	public $height;						// высота калитки
	public $len;							//ширина калитки
	public $nastil;						// тип покрытия профлист, штакет ... рабица... 
	public $nastil_text;		
	public $avtomatika;				//автоматика для ворот
	public $avtomatika_text;		
	public $tumba;						//опорная тумба для откатных ворот
	public $tumba_text;				
	public $focus_input;		

    public static function tableName()
    {
        return '{{element_type}}';
    }
	

	
	public function attributeLabels()
    {
        return [
			'len'=>'Длина ворот',
			'height'=>'Высота ворот',
			'nastil'=>'Тип ворот',	
			'avtomatika'=>'Комплект автоматики',
			'tumba'=>'Опорная тумба'
        ];
    }
	
	
	
	public function scenarios()
  {
      $scenarios = parent::scenarios();
		$scenarios['step1'] = ['len','height','nastil','avtomatika','tumba', 'focus_input'];
        return $scenarios;
  }
  
  
    public function rules()
    {
        return 
		[
			[['tumba','nastil'],'string'],
			[['len','height'], 'double'],
			[[], 'integer'],
			[['len','height','tumba'], 'required','on'=>'step1']
        ];
    }

	
	
	
	
	public function calcInit1() { // устанавливаем значения при первом открытии формы
		$this->nastil_text=$this->readNastil(1);
		$this->tumba='tumba_net';
		$this->avtomatika='avtomatika_no';
		$this->tumba_text=$this->readElements(4000,1);			
		$this->avtomatika_text=['avtomatika_yes'=>'ДА','avtomatika_no'=>'НЕТ'];	
		return;
	}
	
	public function calcStep1() { 	// расчет после изменения любого из значений формы
		$this->nastil_text=$this->readNastil(1);
		$this->tumba_text=$this->readElements(4000,1);			
		$this->avtomatika_text=['avtomatika_yes'=>'ДА','avtomatika_no'=>'НЕТ'];		

		return;
	}
	
	
	/*
		public function readNastil($flag)  { //читаем список с таблици element

		if ($flag==0) {
		 $element = Element::find()->orderBy('id')->asArray()->all();
		} else {
		 $element = Element::find()->select(['attr','name'])->where(['flag' => $flag])->orderBy('id')->asArray()->all();	
		}
		$arr=array();
		foreach ($element as $item) {
			
			$arr[$item['attr']]=$item['name'];
			
		}
		return $arr;
	  }
	  
	  public function readSmetaForZaborType ($id_client) {
		  $items=$this->readNastil(1);
		  $arr=array_keys( $items);
	  
		  //выбираем тип покрытия для калитки из уже выбранных клиентов вариантов и записанных в смету
		   $element = Smeta::find()->select(['zabortype'])->where(['user_id' => $id_client,'zabortype'=>$arr])->orderBy('id')->asArray()->one();	
			return $element['zabortype'];   
	  }
	  	  
	  
	public function getvorota($attr,$h) {
		$h=$h*100;
		$element = Element_Type::find()->select(['id','name', 'kol','attr','price'])->andWhere(['like','attr',$attr])->
		andWhere('attr >="'.($attr.$h).'"')->orderBy('attr')->limit(1)->asArray()->all();
		return $element['0'];
	}
	
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
				$inputdata->type_calc="otkatnievorota";
				$inputdata->user_id=$c;
				$inputdata->smeta_id=$s;
				$inputdata->input_attr= $i;
				$inputdata->input_label=$item;
				$inputdata->input_data=$this->$i;
				$inputdata->datetime=date("Y-m-d H:i:s");
				$inputdata->save(false);
			}
	
			
			// ******************************************************************************
			
			//***************************  СОХРАНЯЕМ СМЕТУ НА  ОТКАТНЫЕ ВОРОТА  ***********************
			
				// ########  сохраняем тип ворот: профлист, рабица, штакет...
				
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
					$smeta->attr   = $this->nastil; 
					$smeta->type  = $s; 
					$smeta->kol  = 0;
					$smeta->ed='';
					$smeta->price =  0;
					$smeta->table = 'info';	//признак информационной записи
					$smeta->insert(false); 

				// END ######## 	сохраняем тип ворот: профлист, рабица, штакет...				

				// ######## каркас ворот -  выбираем стоимость ворот в зависимости от размера
					
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='otkatnievorota';
				
				
				//определяем размер ворот в большую сторону от определенных вариантов  2.7 = 3 м ворота
				if ($this->len<=3) $lenvorota='3m'; else
					if ($this->len<=3.5) $lenvorota='3_5m'; else
						if ($this->len<=4) $lenvorota='4m'; else
							if ($this->len<=4.5) $lenvorota='4_5m'; else
							   if ($this->len<=5) $lenvorota='5m'; else  $lenvorota='5m';
		
				$sel='vorota_otkatnie_'.$lenvorota.'-'; 
				
				$kal=$this->getvorota($sel,$this->height);
				$smeta->attr   = $kal['attr']; // выбраны ворота		
				$smeta->type  = ', Размер '.$this->len.'м*'.$this->height.'м ';			
				$smeta->kol  = 1;
				$smeta->ed='шт.';								
				$smeta->price =  $kal['price'];
				$smeta->table = 'e1';	//признак материалов
				$smeta->insert(false); 

				// END ######## каркас откатных ворот 
				
				
				// ######## ролики для откатных ворот в смету

				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
				$smeta->attr   =  'vorota_otkatnie_roliki_'.$lenvorota;
				$smeta->type  = '';
				$smeta->kol  = 1;
				$smeta->ed='шт.';
				$smeta->price =  $this->getelement_price($smeta->attr);
				$smeta->table = 'e2';	//признак работ
				$smeta->insert(false); 
				
				// END ######## ролики для откатных работ в смету
				
				
				// ########  работы по установке откатных ворот  в смету работ

				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
				$smeta->attr   =  $kal['attr'];
				$smeta->type  = '';
				$smeta->kol  = 1;
				$smeta->ed='';
				$smeta->price =  $this->getwork_price($smeta->attr);
				$smeta->table = 'w1';	//признак работ
				$smeta->insert(false); 
				
				// END ######## откосов на ворота		
				
				
				// ######## автоматика для откатных ворот
				
				if ($this->avtomatika=='avtomatika_yes' ) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
					
					//в зависимости от длины откатных ворот подбираем автоматику
					
					if (($lenvorota=='3m') ||  ($lenvorota=='3_5m') ||  ($lenvorota=='4m')) {
						$smeta->attr   ='vorota_otkatnie_avtomatika_1';
					} else { 
						$smeta->attr   ='vorota_otkatnie_avtomatika_2';
					};
					$smeta->type  = ' '; 
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e3';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## в зависимости от длины откатных ворот подбираем автоматику
				
				// ########  работы по установке автоматики откатных ворот  
				if ($this->avtomatika=='avtomatika_yes' ) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
					$smeta->attr   =  'vorota_otkatnie_avtomatika_1';
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w2';	//признак работ
					$smeta->insert(false); 
				};
				// END ######## работы по установке автоматики откатных ворот  	

				
				if ($this->tumba!='tumba_net' ) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
					
					//выбираем тип тумбы
					$smeta->attr =$this->tumba;					
					$smeta->type  = ' '; 
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e4';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## в зависимости от длины откатных ворот подбираем автоматику
				
				// ########  работы по установке автоматики откатных ворот  
				if ($this->tumba!='tumba_net') {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
					$smeta->attr   =$this->tumba;
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w3';	//признак работ
					$smeta->insert(false); 
				};
				// END ######## работы по установке автоматики откатных ворот  		
				
				// ***************** ДОБАВЛЯЕМ МИНУСА ПО ЛАГАМ, СТОЛБАМ, КРЫШКАМ, УКОСИНАМ, ОТКОСАМ, РАБОТАМ ****************

				// ######## добавляем минус по столбам
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='otkatnievorota';
					$smeta->attr   = 'minus-stolb';
					$smeta->type  = $this->nastil;
					$smeta->kol  = -2;
					$smeta->ed='';
					$smeta->price =0;
					$smeta->table = 'minus';	//признак материалов
					$smeta->insert(false); 			

				// END ######## добавляем минус по столбам									
			//************************ КОНЕЦ СОХРАНЯЕМ СМЕТУ НА ОТКАТНЫЕ ВОРОТА *********************	
			
				$smeta->readSmeta($c,$s);
				
			} else {
			
					$this->calc_step=1;
					$this->calcStep1();
			}		
		
	}
	
}

?>