<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class CalcVorota  extends FuncZabor {
	
	const STEP1='step1';
	
	public $height;						// высота калитки
	public $len;							//ширина калитки
	public $nastil;						// тип покрытия профлист, штакет ... рабица... 
	public $nastil_text;	
	public $type;							// тип калитки
	public $type_text;
	public $stolb_razmer;			// размер столбов калитки
	public $stolb_razmer_text;	
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
	public $stopor;
	public $stopor_text;				//стопор на ворота
	public $ruchka;					//ручка на калитку
	public $ruchka_text;
	public $otkrivanie;
	public $otkrivanie_text;
	public $stolb_grunt;
	public $stolb_grunt_text;
	public $stolb_okraska;
	public $stolb_okraska_text;
	public $pokritie;
	public $pokritie_text;
	public $stolb_zaglub;
	public $stolb_zaglub_text;
	public $calc_step=1;
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
			'type'=>'Тип ворот',				
			'stolb_razmer'=>'Размер столба',						
			'stajka'=>'Стяжка',				
			'ukosin_count'=>'Укосин',		
			'otkos_count'=>'Откосов',				
			'zamok'=>'Замок',					
			'zadvigka'=>'Задвижка',			
			'ruchka'=>'Ручка',
			'otkrivanie'=>'Открывание',
			'stolb_grunt'=>'Грунтование',
			'stolb_okraska'=>'Окраска',
			'pokritie'=>'Тип покрытия ворот',
			'stolb_zaglub'=>'Заглубление'
        ];
    }
	
	
	
	public function scenarios()
  {
      $scenarios = parent::scenarios();
		$scenarios['step1'] = ['len','height','nastil','type','stolb_razmer','stajka','ukosin_count','otkos_count','zamok','zadvigka',
		'ruchka','stopor','otkrivanie','stolb_grunt', 'stolb_okraska', 'pokritie','stolb_zaglub', 'focus_input'];
        return $scenarios;
  }
  
  
    public function rules()
    {
        return 
		[
			[['type','nastil','stolb_razmer','stajka','zamok','zadvigka','stopor','ruchka','otkrivanie', 'stolb_grunt', 'stolb_okraska',
			'focus_input','ukosin_count','otkos_count','pokritie','stolb_zaglub'],'string'],
			[['len','height'], 'double'],
			[[], 'integer'],
			[['len','height'], 'required','on'=>'step1']
        ];
    }

	
	
	
	public function calc_vororta() {

		 
	 return;	
	}
	
	
	
	
	public function calcInit1() { // устанавливаем значения при первом открытии формы
		$this->nastil_text=$this->readNastil(1);
		$this->type_text=$this->readElements(3500,1);			
		$this->stolb_razmer_text=$this->readElements(3500,2);	
		$this->stajka_text=$this->readElements(3500,3);
		$this->stajka='stajka_vorota_da';
		$this->zamok_text=$this->readElements(3500,4);
		$this->zamok='vorota_zamok_net';
		$this->zadvigka_text=$this->readElements(3500,5);	
		$this->stopor_text=$this->readElements(3500,6);				
		$this->ruchka_text=$this->readElements(3500,7);				
		$this->ukosin_count_text=$this->readElements(3500,8);			
		$this->otkos_count_text=$this->readElements(3500,9);			
		$this->otkrivanie_text=$this->readElements(3500,12);		
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
		$this->type_text=$this->readElements(3500,1);		
		$this->stolb_razmer_text=$this->readElements(3500,2);	
		$this->stajka_text=$this->readElements(3500,3);
		$this->zamok_text=$this->readElements(3500,4);
		$this->zadvigka_text=$this->readElements(3500,5);	
		$this->stopor_text=$this->readElements(3500,6);				
		$this->ruchka_text=$this->readElements(3500,7);				
		$this->ukosin_count_text=$this->readElements(3500,8);			
		$this->otkos_count_text=$this->readElements(3500,9);	
		$this->otkrivanie_text=$this->readElements(3500,12);				
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
		 $element = Element_Type::find()->select(['attr','name','ord'])->where(['type' => $type])->orderBy(['ord'=>SORT_ASC])->asArray()->all();
		} else {
		 $element = Element_Type::find()->select(['attr','name','ord'])->where(['type' => $type,'sub_type' => $subtype])->orderBy(['ord'=>SORT_ASC])->asArray()->all();	
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
				$inputdata->type_calc="vorota";
				$inputdata->user_id=$c;
				$inputdata->smeta_id=$s;
				$inputdata->input_attr= $i;
				$inputdata->input_label=$item;
				$inputdata->input_data=$this->$i;
				$inputdata->datetime=date("Y-m-d H:i:s");
				$inputdata->save(false);
			}
	
			
			// ******************************************************************************
			
			//***************************  СОХРАНЯЕМ СМЕТУ НА ВОРОТА  ***********************
			
				// ########  сохраняем тип ворот: профлист, рабица, штакет...
				
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   = $this->nastil; 
					$smeta->type  = $s; 
					$smeta->kol  = 0;
					$smeta->ed='';
					$smeta->price =  0;
					$smeta->table = 'info';	//признак информационной записи
					$smeta->insert(false); 

				// END ######## 	сохраняем тип ворот: профлист, рабица, штакет...	

				// ######## каркас калитки - в зависимости от количества столбов и типа выбираем стоимость калитки
					
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='vorota';
				$sel=''; 
				$pred='';
				
				//определяем размер ворот в большуб сторону от определенных вариантов  2.7 = 3 м ворота
				if ($this->len<=3) $lenvorota='3m'; else
					if ($this->len<=3.5) $lenvorota='3_5m'; else
						if ($this->len<=4) $lenvorota='4m'; else
							if ($this->len<=4.5) $lenvorota='4_5m'; else
							   if ($this->len<=5) $lenvorota='5m'; else  $lenvorota='5m';
				
				if ($this->type=='vorota_standart' ) { //выбран тип калитки стандарт
					$pred='vorota';
					$sel=$pred.$lenvorota;
					
				} else {
					$pred='vorotarama';
					$sel=$pred.$lenvorota;
				};
				$sel.='_2stolb_';
				
				if ($this->stolb_razmer=='vorota_stolb_100x100' ) { //выбран тип калитки стандарт
					$sel.='100*100-'; 
				} else {
					$sel.='80*80-';	
				};						
				
				
				//формируем стоимость за грунтовку и покраску каркаса
				//если грунтовать и красить , то грунтуем и красим 1-откосы, 2-перемычку, 3-каркас калитки 
				$karkas_price=0;
				$otkos_price=0;
				$perekladina_price=0;
				$karkas_len=$this->getelement_price('plus_metrov_vorota_'.$lenvorota); //сколько метров добавлять к грунтовке и покраске
				if ($this->stolb_grunt != 'grunt_none') {
					$karkas_price=$karkas_len*$this->getwork_price('grunt_stolb');
					$otkos_price=substr($this->otkos_count, -1)*$this->getelement('otkos')*$this->getwork_price('grunt_stolb');
					$perekladina_price = ($this->stajka=='stajka_vorota_da') ? $this->getelement('stajka_vorota_za_metr_1')*$this->len*$this->getwork_price('grunt_stolb') : 0;
				}
				
				if ($this->stolb_okraska != 'kraska_none') {
					$karkas_price+=$karkas_len*$this->getwork_price('pokraska_stolb');
					$otkos_price+=substr($this->otkos_count, -1)*$this->getelement('otkos')*$this->getwork_price('pokraska_stolb');
					$perekladina_price+= ($this->stajka=='stajka_vorota_da') ? $this->getelement('stajka_vorota_za_metr_1')*$this->len*$this->getwork_price('pokraska_stolb') : 0;
				}
				

				//Yii::warning($this->getvorota($sel,$this->height)); 

				$kal=$this->getvorota($sel,$this->height);
	

				$smeta->attr   = $kal['attr']; // выбраны ворота		
				
				$smeta->type  =', Размер '.$this->len.'м*'.$this->height.'м ( '.$this->getelement_name($this->pokritie).', '.
				$this->getelement_name($this->stolb_grunt).',  '.$this->getelement_name($this->stolb_okraska).', '.$this->getelement_name($this->otkrivanie).')';
				
				$smeta->kol  = 1;
				$smeta->ed='шт.';
				
				
				$smeta->price =  $kal['price'];
				
				//$q='karkas_len='.$karkas_len.'---karkas='.$karkas_price .'------otkos='. $otkos_price .'------perekladina='. $perekladina_price.'----|'; 
				 
				$smeta->price = $smeta->price + $karkas_price ;
			
				$smeta->table = 'e1';	//признак материалов
				$smeta->insert(false); 

				// END ######## каркас ворот
		
		
				// ######## несьемная стяжка на ворота
				
				if ($this->stajka=='stajka_vorota_da' ) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   = 'stajka_vorota_za_metr_1'; 
					$smeta->type  = ' (несъемная)'; //'***'.$this->stolb_grunt.'***'.$q;
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr)*$this->len+$perekladina_price;
					$smeta->table = 'e2';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## несьемная стяжка на ворота	
				
		
				// ######## замок на ворота
				
				if ($this->zamok=='vorota_zamok_da' ) { 
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   = 'zamok_vorota_1'; 
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e3';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## замок на ворота	
				
				// ######## стопор на ворота
				
				if ($this->stopor=='stopor_vorota_da' ) { 
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   = 'stopor_vorota_1'; 
					$smeta->type  = '';
					$smeta->kol  = 2;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e8';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## стопор на ворота	
				
				
				// ######## задвижка на ворота
				
				if ($this->zadvigka=='zadvijka_vorota_da' ) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   = 'zadvigka_vorota_1'; 
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e4';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## задвижка на ворота					

				// ######## ручка на ворота
				
				if ($this->ruchka=='ruchka_vorota_da' ) { // выбран тип калитки стандарт
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   = 'ruchka_vorota_1'; 
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e5';	//признак материалов
					$smeta->insert(false); 
				};
				// END ######## ручка на ворота					

				// ######## укосин на калитку
					$tmp=	substr($this->ukosin_count, -1);	
					if ($tmp>0) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
						$smeta->attr   = 'ukosina';
						$smeta->type  = '';
						$smeta->kol  = $tmp;
						$smeta->ed='шт.';
						$smeta->price =  $this->getelement_price($smeta->attr);
						$smeta->table = 'e6';	//признак материалов
						$smeta->insert(false); 
					}
				// END ######## укосин на ворота				

				// ######## откосов на калитку
				
					$tmp=	substr($this->otkos_count, -1);	
					if ($tmp>0) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
						$smeta->attr   = 'otkos';
						$smeta->type  = '';
						$smeta->kol  = $tmp;
						$smeta->ed='шт.';
						$smeta->price =  $this->getelement_price($smeta->attr)+ $otkos_price;
						$smeta->table = 'e7';	//признак материалов
						$smeta->insert(false); 
					}
				// END ######## откосов на ворота						


				// ######## записать работы по установке откосов в смету работ
					$tmp=	substr($this->otkos_count, -1);	
					if ($tmp>0) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
						$smeta->attr   = 'otkos_vorota';
						$smeta->type  = '';
						$smeta->kol  = $tmp;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w1';	//признак работ
						$smeta->insert(false); 
					}
				// END ######## откосов на ворота		

				// ######## записать работы по установке укосин в смету работ
					$tmp=	substr($this->ukosin_count, -1);	
					if ($tmp>0) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
						$smeta->attr   = 'ukosina';
						$smeta->type  = '';
						$smeta->kol  = $tmp;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w1';	//признак работ
						$smeta->insert(false); 
					}
				// END ######## откосов на ворота		
				
				// ######## записать работы по установке стяжки в смету работ
					if ($this->stajka=='stajka_vorota_da' ) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
						$smeta->attr   = 'stajka_vorota';
						$smeta->type  = ' ';
						$smeta->kol  = 1;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w2';	//признак работ
						$smeta->insert(false); 
					}
				// END ######## стяжка на ворота		


				// ######## записать работы по установке замка в смету работ
					if ($this->zamok=='vorota_zamok_da' ) { 
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
						$smeta->attr   = 'zamok_vorota_1';
						$smeta->type  = '';
						$smeta->kol  = 1;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w3';	//признак работ
						$smeta->insert(false); 
					}
				// END ######## замок на ворота				

				// ######## записать работы по установке задвижки в смету работ
					if ($this->zadvigka=='zadvijka_vorota_da' ) {
						$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
						$smeta->attr   = 'zadvijka_vorota_1';
						$smeta->type  = '';
						$smeta->kol  = 1;
						$smeta->ed='';
						$smeta->price =  $this->getwork_price($smeta->attr);
						$smeta->table = 'w4';	
						$smeta->insert(false); 
					}
				// END ######## задвижки на вороту		

				// ######## записать работы по установке ручки в смету работ
					if ($this->ruchka=='ruchka_vorota_da') {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   = 'ruchka_vorota_1';
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w5';	//признак материалов
					$smeta->insert(false); 
					}
				// END ######## ручка на ворота		

				// ######## записать работы по установке ворот. выбор стоимости с заглублением или без
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					if ($this->stolb_zaglub=='zaglub_zabor_net') $smeta->attr   = $pred.'_'.$lenvorota.'_bez_zag';  else $smeta->attr   = $pred.'_'.$lenvorota.'_zag' ;					 
					$smeta->type  = '';
					$smeta->kol  = 1;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w6';	//признак материалов
					$smeta->insert(false); 
				// END ######## работы по установке ворот. выбор стоимости с заглублением или без	


				// ######## записать работы по установке стопора
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					if ($this->stopor=='stopor_vorota_da' ) { 
					 $smeta->attr   = 'stopor_vorota_1'; 
					 $smeta->kol  = 2;
					 $smeta->ed='';
					 $smeta->price =  $this->getwork_price($smeta->attr);
					 $smeta->table = 'w7';	//признак материалов
					 $smeta->insert(false); 
					}
				// END ######## работы по установке ворот. выбор стоимости с заглублением или без	

				// ***************** ДОБАВЛЯЕМ МИНУСА ПО ЛАГАМ, СТОЛБАМ, КРЫШКАМ, УКОСИНАМ, ОТКОСАМ, РАБОТАМ ****************

				// ######## добавляем минус по столбам
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='vorota';
					$smeta->attr   ='minus-stolb';
					$smeta->type  = $this->nastil;
					$smeta->kol  = -2;
					$smeta->ed='шт.';
					$smeta->price =0;
					$smeta->table = 'minus';	//признак материалов
					$smeta->insert(false); 			

				// END ######## добавляем минус по столбам					
				
			//************************ КОНЕЦ СОХРАНЯЕМ СМЕТУ НА ВОРОТА *********************	

				$smeta->readSmeta($c,$s);
				
			} else {
			
					$this->calc_step=1;
					$this->calcStep1();
			}
}	
	
}

?>