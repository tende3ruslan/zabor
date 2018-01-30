<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class CalcKalitka  extends FuncZabor {
	
	const STEP1='step1';
	
	public $height;						// высота калитки
	public $len;							//ширина калитки
	public $nastil;						// тип покрытия профлист, штакет ... рабица... 
	public $nastil_text;
	public $type;							// тип каркаса калитки 
	public $type_text;
	public $stolb_razmer;			// размер столбов калитки
	public $stolb_razmer_text;	
	public $stolb_count;				// количество столбов калитки
	public $stolb_count_text;				
	public $open_type;				//открытие калитки внутрь или наружу
	public $open_type_text;
	public $side;							// сторона калитки правая или левая
	public $side_text;
	public $stajka;						// стяжка несьемная
	public $stajka_text;
	public $ukosin_count;			//количество укосин
	public $ukosin_count_text;			
	public $otkos_count;			//количество откосов 0,1,2
	public $otkos_count_text;			
	public $zamok;					//замок на калитку
	public $zamok_text;
	public $zadvigka;					//задвижка на калитку
	public $zadvigka_text;
	public $ruchka;					//ручка на калитку
	public $ruchka_text;
	public $stolb_grunt;
	public $stolb_grunt_text;
	public $stolb_okraska;
	public $stolb_okraska_text;
	public $pokritie;
	public $pokritie_text;
	public $stolb_zaglub;
	public $stolb_zaglub_text;
	public $kalitka_otdelno;
	public $calc_step=1;
	public $focus_input;
	
		

    public static function tableName()
    {
        return '{{element_type}}';
    }
	

	
	public function attributeLabels()
    {
        return [
			'len'=>'Длина калитки',
			'height'=>'Высота калитки',
			'nastil'=>'Тип калитки',
			'type'=>'Тип каркаса',				
			'stolb_razmer'=>'Размер столба',		
			'stolb_count'=>'Количество столбов',			
			'open_type'=>'Тип открытия',			
			'side'=>'Сторона',					
			'stajka'=>'Стяжка',				
			'ukosin_count'=>'Укосин',		
			'otkos_count'=>'Откосов',				
			'zamok'=>'Замок',					
			'zadvigka'=>'Задвижка',			
			'ruchka'=>'Ручка',
			'kalitka_otdelno'=>'Отдельностоящая',
			'stolb_grunt'=>'Грунтование',
			'stolb_okraska'=>'Окраска',
			'pokritie'=>'Тип покрытия калитки',
			'stolb_zaglub'=>'Заглубление'
        ];
    }
	
	
	
	public function scenarios()
  {
      $scenarios = parent::scenarios();
		$scenarios['step1'] = ['len','height','nastil','type','stolb_razmer','stolb_count','open_type','side','stajka','ukosin_count','otkos_count','zamok','zadvigka',
		'ruchka', 'kalitka_otdelno', 'stolb_grunt', 'stolb_okraska', 'pokritie','stolb_zaglub', 'focus_input'];
        return $scenarios;
  }
  
  
    public function rules()
    {
        return 
		[
			[['nastil', 'type','stolb_razmer','open_type','side','stajka','zamok','zadvigka','ruchka','kalitka_otdelno', 'stolb_grunt', 'stolb_okraska', 'focus_input','ukosin_count','otkos_count','stolb_count','pokritie','stolb_zaglub'],'string'],
			[['len','height'], 'double'],
			[[], 'integer'],
			[['type','stolb_razmer','stolb_count','open_type','side','stajka','zamok','zadvigka','ruchka'], 'required','on'=>'step1']
        ];
    }

	
	
	
	public function calc_kalitka() {

		 
	 return;	
	}
	
	
	
	
	public function calcInit1() { // устанавливаем значения при первом открытии формы
		$this->nastil_text=$this->readNastil(1);
		$this->type_text=$this->readElements(1000,2);			
		$this->stolb_razmer_text=$this->readElements(1200,13);	
		$this->open_type_text=$this->readElements(1000,3);
		$this->side_text=$this->readElements(1000,4);
		$this->stajka_text=$this->readElements(1000,5);
		$this->zamok_text=$this->readElements(1000,6);
		$this->zadvigka_text=$this->readElements(1000,7);	
		$this->stolb_count_text=$this->readElements(1000,8);			
		$this->ruchka_text=$this->readElements(1000,9);				
		$this->ukosin_count_text=$this->readElements(1000,10);			
		$this->otkos_count_text=$this->readElements(1000,11);			
		$this->stolb_grunt_text=$this->readElements(9,0);							// грунтуем
		$this->stolb_okraska_text=$this->readElements(12,1);					// окрашиваем
		
		if ($this->nastil=='proflist')
			$this->pokritie_text=$this->readElements(5,0)+$this->readElements(6,0)+$this->readElements(7,0)+$this->readElements(8,0);
		if ($this->nastil=='shtaket')
			$this->pokritie_text=$this->readElements(7500,1);
		
		$this->stolb_zaglub_text=$this->readElements(1200,200);
		//если выбрано покрытие 3 в 1  то выбор краски меняем на выбор цвета
		if ($this->stolb_grunt=='pokritie_3v1') {
			$this->stolb_okraska_text=$this->readElements(10,1)+$this->readElements(10,2);				
		}		

		return;
	}
	
	public function calcStep1() { 	// расчет после изменения любого из значений формы
		$this->nastil_text=$this->readNastil(1);										
		$this->type_text=$this->readElements(1000,2);			
		$this->stolb_razmer_text=$this->readElements(1200,13);	
		$this->open_type_text=$this->readElements(1000,3);
		$this->side_text=$this->readElements(1000,4);
		$this->stajka_text=$this->readElements(1000,5);
		$this->zamok_text=$this->readElements(1000,6);
		$this->zadvigka_text=$this->readElements(1000,7);	
		$this->stolb_count_text=$this->readElements(1000,8);			
		$this->ruchka_text=$this->readElements(1000,9);				
		$this->ukosin_count_text=$this->readElements(1000,10);			
		$this->otkos_count_text=$this->readElements(1000,11);	
		$this->stolb_grunt_text=$this->readElements(9,0);							// грунтуем
		$this->stolb_okraska_text=$this->readElements(12,1);					// окрашиваем		
		
		if ($this->nastil=='proflist')
			$this->pokritie_text=$this->readElements(5,0)+$this->readElements(6,0)+$this->readElements(7,0)+$this->readElements(8,0);
		if ($this->nastil=='shtaket')
			$this->pokritie_text=$this->readElements(7500,1);
		
		$this->stolb_zaglub_text=$this->readElements(1200,200);
		//если выбрано покрытие 3 в 1  то выбор краски меняем на выбор цвета
		if ($this->stolb_grunt=='pokritie_3v1') {
			$this->stolb_okraska_text=$this->readElements(10,1)+$this->readElements(10,2);				
		}
		// если отдельностоящая то всегда два столба
		if ($this->kalitka_otdelno) $this->stolb_count='kalitka_stolb_count_2';	
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
	  
	  
	  
	
	public function getkalitka($attr,$h) {
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
		 $element = Element_Type::find()->select(['attr','name'])->where(['type' => $type])->orderBy('ord')->asArray()->all();
		} else {
		 $element = Element_Type::find()->select(['attr','name'])->where(['type' => $type,'sub_type' => $subtype])->orderBy('ord')->asArray()->all();	
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
				$inputdata->type_calc="kalitka";
				$inputdata->user_id=$c;
				$inputdata->smeta_id=$s;
				$inputdata->input_attr= $i;
				$inputdata->input_label=$item;
				$inputdata->input_data=$this->$i;
				$inputdata->datetime=date("Y-m-d H:i:s");
				$inputdata->save(false);
			}
	
			
			// ******************************************************************************
			
			//***************************  СОХРАНЯЕМ СМЕТУ НА КАЛИТКУ  ***********************
			
				// ########  сохраняем тип калитки: профлист, рабица, штакет...
				
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = $this->nastil; 
					$smeta->type  = $s; 
					$smeta->kol  = 0;
					$smeta->ed='';
					$smeta->price =  0;
					$smeta->table = 'info';	//признак информационной записи
					$smeta->insert(false); 

				// END ######## несьемная стяжка на калитку	
				
				

				// ######## каркас калитки - в зависимости от количества столбов и типа выбираем стоимость калитки
					
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='kalitka';
				$sel='';
				
				
				if ($this->type=='kalitka_standart' ) { //выбран тип калитки стандарт
					$sel='kalitka_';
					$t='Калитка ';
					$karkas_len=$this->getelement_price('material_kalitka');  // количество материалов - вместо цены в базе указано количество для калитки стандарт
				} else {
					$sel='kalitkarama_';
					$karkas_len=$this->getelement_price('material_kalitkarama'); // количество материалов - вместо цены в базе указано количество для калитки в раме
				};

				if ($this->stolb_count=='kalitka_stolb_count_2' ) { //выбран тип калитки стандарт
					$sel.='2stolb_';
					$karkas_len+=$this->getelement_price('plus_pokraska_2stolb');
				} else {
					$sel.='1stolb_';
					$karkas_len+=$this->getelement_price('plus_pokraska_1stolb');
				};				
				
				if ($this->stolb_razmer=='stolb_100*100' ) { //выбран тип калитки стандарт
					$sel.='100*100-'; 
				} else {
					$sel.='80*80-';	
				};						
				

				//формируем стоимость за грунтовку и покраску каркаса
				//если грунтовать и красить , то грунтуем и красим 1-откосы, 2-перемычку, 3-каркас калитки 
				
				if ($this->stolb_grunt != 'grunt_none') {
					$karkas_price=$karkas_len*$this->getwork_price('grunt_stolb');
					$otkos_price=$this->getelement($this->otkos_count)*$this->getelement('laga_40_20')*$this->getwork_price('grunt_stolb');
					 $perekladina_price = ($this->stajka=='stazka_da') ? $this->getelement('stajka_kalitka')*$this->getwork_price('grunt_stolb') : 0;
				}
				
				if ($this->stolb_okraska != 'kraska_none') {
					$karkas_price+=$karkas_len*$this->getwork_price('pokraska_stolb');
					$otkos_price+=$this->getelement($this->otkos_count)*$this->getelement('laga_40_20')*$this->getwork_price('pokraska_stolb');
					$perekladina_price+= ($this->stajka=='stazka_da') ? $this->getelement('stajka_kalitka')*$this->getwork_price('pokraska_stolb') : 0;
				}
				

				//Yii::warning($this->getkalitka($sel,$this->height)); 
				
				$kal=$this->getkalitka($sel,$this->height);

				$smeta->attr   = $kal['attr']; // выбрана калитка		

				$smeta->type  =  ', Размер '.$this->len.'м*'.$this->height.'м ( '.$this->getelement_name($this->pokritie).', '.$this->getelement_name($this->type).', '.$this->getelement_name($this->open_type).', '.
				$this->getelement_name($this->side).', '.$this->getelement_name($this->stolb_grunt).', '.$this->getelement_name($this->stolb_okraska).')';
				
				$smeta->kol  = 1;
				$smeta->ed='шт.';
				
				
				$smeta->price =  $kal['price']+$karkas_price;

			
				$smeta->table = 'e1';	//признак материалов
				$smeta->insert(false); 

				// END ######## каркас калитки
		
		
				// ######## несьемная стяжка на калитку
				
				if ($this->stajka=='stazka_da' ) { 
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'stajka_kalitka'; 
					$smeta->type  = ''; //'***'.$this->stolb_grunt.'***'.$q;
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr) + $perekladina_price;
					$smeta->table = 'e2';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## несьемная стяжка на калитку	
				
		
				// ######## замок на калитку
				
				if ($this->zamok=='zamok_da' ) { // выбран тип калитки стандарт
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'zamok_titan'; 
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e3';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## замок на калитку	
				
				// ######## задвижка на калитку
				
				if ($this->zadvigka=='zadvigka_da' ) { 
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'zadvigka_cink'; 
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e4';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## задвижка на калитку					

				// ######## ручка на калитку
				
				if ($this->ruchka=='ruchka_da' ) { 
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'ruchka_kalitka'; 
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e5';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## ручка на калитку					

				// ######## укосин на калитку
					$tmp=	substr($this->ukosin_count, -1);	
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'ukosina';
					$smeta->type  = '';
					$smeta->kol  = $tmp;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e6';	//признак материалов
					$smeta->insert(false); 

				// END ######## укосин на калитку				

				// ######## откосов на калитку
				
					$tmp=	substr($this->otkos_count, -1);	
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'otkos';
					$smeta->type  = '';
					$smeta->kol  = $tmp;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr)+ $otkos_price ;
					$smeta->table = 'e7';	//признак материалов
					$smeta->insert(false); 
				// END ######## откосов на калитку						


				// ######## записать работы по установке откосов в смету работ
					$tmp=	substr($this->otkos_count, -1);	
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'otkos_ustanovka';
					$smeta->type  = '';
					$smeta->kol  = $tmp;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w1';	//признак материалов
					$smeta->insert(false); 
				// END ######## откосов на калитку		


				// ######## записать работы по установке стяжки в смету работ
					if ($this->stajka=='stazka_da' ) { 
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
						$smeta->attr   = 'stajka_kalitka';
						$smeta->type  = ' (Труба 40х20 1310)';
						$smeta->kol  = 1;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w2';	//признак материалов
						$smeta->insert(false);
					}					
				// END ######## стяжка на калитку		


				// ######## записать работы по установке замка в смету работ
				if ($this->zamok=='zamok_da' ) { 
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'zamok_kalitka';
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w3';	//признак материалов
					$smeta->insert(false); 
				}
				// END ######## замок на калитку				

				// ######## записать работы по установке задвижки в смету работ
					if ($this->zadvigka=='zadvigka_da' ) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
						$smeta->attr   = 'zadvigka_kalitka';
						$smeta->type  = '';
						$smeta->kol  = 1;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w4';	
						$smeta->insert(false); 
					}
				// END ######## задвижки на калитку		

				// ######## записать работы по установке ручки в смету работ
					if ($this->ruchka=='ruchka_da' ) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
						$smeta->attr   = 'ruchka_kalitka';
						$smeta->type  = '';
						$smeta->kol  = 1;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w5';	
						$smeta->insert(false); 
					}
				// END ######## ручка на калитку		

				// ######## записать работы по установке калитки. выбор стоимости с заглублением или без
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					if ($this->stolb_zaglub=='zaglub_zabor_net') $smeta->attr   = 'kalitka_karkas_zaglub_net';  else $smeta->attr   = 'kalitka_karkas_zaglub_da' ;
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w6';	
					$smeta->insert(false); 
				// END ######## работы по установке калитки. выбор стоимости с заглублением или без		
				
				
				// ***************** ДОБАВЛЯЕМ МИНУСА ПО ЛАГАМ, СТОЛБАМ, КРЫШКАМ, УКОСИНАМ, ОТКОСАМ, РАБОТАМ ****************

				// ######## добавляем минус по столбам
				if (!$this->kalitka_otdelno)  { 							//если НЕ отдельно стоящая калитка 
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='kalitka';
					$smeta->attr   = 'minus-stolb';
					$smeta->type  = $this->nastil;
					$smeta->kol  = -1;
					$smeta->ed='';
					$smeta->price =0;
					$smeta->table = 'minus';	//признак материалов
					$smeta->insert(false); 						
				}
				// END ######## добавляем минус по столбам							
				

				
				
			//************************ КОНЕЦ СОХРАНЯЕМ СМЕТУ НА КАЛИТКУ *********************	
				$smeta->readSmeta($c,$s);
				
			} else {
			
					$this->calc_step=1;
					$this->calcStep1();
			}	
	
}
	
}

?>