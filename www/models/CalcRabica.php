<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class CalcRabica  extends FuncZabor {
	
	const STEP1='step1';

	
	
	public $calc_step;
	public $focus_input;
	
	public $len;							 //длина забора
	public $height;						//высота забора
	public $stolb_nado;				//нужны ли столбы
	public $laga_nado;				//нужны ли лаги	
	public $rabica_nado;				//только рабица
	public $rabota_nado;				//только работы по установке забора
	public $stolb_razmer;			 //массив из таблицы element_type для type=1  
	public $stolb_razmer_text;
	public $stolb_count;				//количество столбов
	public $stolb_len;					//расстояние между столбами
	public $stolb_height;				//высота столбов
	public $stolb_glubina;			//заглубление столба
	public $stolb_glubina_text;
	public $stolb_krishka;			//количество крышек
	public $stolb_grunt;				//грунтовка столбов
	public $stolb_grunt_text;
	public $stolb_okraska;			//столб окраска
	public $stolb_okraska_text;
	public $stolb_butov;				//бутование столба
	public $stolb_butov_text;
	public $stolb_beton;				//бетонирование столба
	public $stolb_beton_text;
	public $stolb_ukosin;				//укосин на столб
	public $stolb_ukosin_kol;		//общее количество укосин
	public $stolb_otkos;				//количество откосов
	public $laga_razmer;				 //размер лаг
	public $laga_razmer_text;
	public $laga_kol;			 		 //количество лаг
	public $laga_vsego;			 	 //количество лаг всего
	public $proflist_type;			 	//тип профлиста
	public $proflist_color;			 //цвет профлиста
	public $proflist_color_text;	 
	public $proflist_type_text;
	public $proflist_count;			//количество профлистов
	public $planka_text;
	public $planka;						//тип планки
	public $planka_count;			//метров планки всего
	public $samorez_count;			//количество саморезов всего
	public $samorez_color;			//цвет самореза
	public $samorez_color_text;
	public $samorez_type;			//тип саморезов
	public $samorez_type_text;	
		

    public static function tableName()
    {
        return '{{rabica}}';
    }
	

	
	public function attributeLabels()
    {
        return [
			'len'=>'Длина забора',
            'height' => 'Высота забора ',
			'stolb_nado' => 'Столбы надо',
			'laga_nado' => 'Лаги надо',
			'rabica_nado' => 'Рабица надо',
			'rabota_nado' => 'Установка надо',
			'stolb_razmer'=>'Размер столба',
			'stolb_len' => 'Расст. между столбами',
			'stolb_height' => 'Высота столбов',
			'stolb_glubina' =>'Заглубление столбов',
			'stolb_count' => 'Количество столбов',
			'stolb_krishka'=>'Крышки',
			'stolb_grunt' => 'Грунтование металл. каркаса',
			'stolb_okraska' =>'Окраска металл. каркаса',
			'stolb_butov'=> 'Бутование столбов', 
			'stolb_beton'=> 'Бетонирование столбов',
			'stolb_ukosin'=>'Укосин на столб',
			'stolb_otkos'=>'Откосов',
			'stolb_ukosin_kol'=>'Всего укосин',
			'armatura_kol'=>'Количество лаг',
			'armatura_vsego'=>'Всего лаг',
        ];
    }
	
	
	
	public function scenarios()
  {
      $scenarios = parent::scenarios();
		$scenarios['step1'] = ['stolb_nado', 'laga_nado', 'rabica_nado','rabota_nado', 'focus_input','len', 'height','stolb_razmer', 'stolb_len','stolb_height','stolb_glubina','stolb_count','stolb_krishka',
		'stolb_grunt','stolb_okraska','stolb_butov', 'stolb_beton', 'stolb_ukosin', 'stolb_ukosin_kol','stolb_otkos', 'armatura_kol','armatura_vsego'];
        return $scenarios;
  }
  
  
    public function rules()
    {
        return 
		[
			[['focus_input'],'string'],
			[['stolb_count','stolb_otkos','armatura_kol','armatura_vsego','proflist_count','planka_count', 'samorez_count'],'integer'],
			[['len', 'height','stolb_len', 'stolb_height'], 'double'],
			[['len', 'height', 'stolb_razmer', 'stolb_len','stolb_height','stolb_glubina','stolb_count','stolb_krishka',
			'stolb_grunt','stolb_okraska','stolb_butov', 'stolb_beton','stolb_ukosin', 'stolb_ukosin_kol'], 'required','on'=>'step1']
        ];
    }

	public function calc_stolb() {

		//определяем количество столбов в зависимости от заданного расстояния между столбами
		$this->stolb_count=ceil ($this->len/$this->stolb_len)+1;						// расстояние между столбами округляем в большую
		$this->stolb_krishka=$this->stolb_count;											// количество крышек
		$this->stolb_ukosin_kol=$this->stolb_ukosin*$this->stolb_count;	//общее число укосин
		$this->laga_vsego=ceil ($this->len*$this->laga_kol/3);					//общее количество лаг
		
		$tmp=0;


		 if(($this->focus_input=='calcrabica-stolb_glubina') || ($this->focus_input=='calcrabica-height') ){
			 $this->stolb_butov='butov_none';
			 $this->stolb_beton='beton_none';
			 //определяем высоту столба как высоту забора + высота заглубления
			 $this->stolb_height=$this->height + $this->getwork($this->stolb_glubina)-0.2;
		 } elseif (( $this->focus_input=='calcrabica-stolb_butov') ) {
			 $this->stolb_glubina='zaglub_none';
			  $this->stolb_beton='beton_none';
  		    //определяем высоту столба как высоту забора + высота бутования
			 $this->stolb_height=$this->height + $this->getwork($this->stolb_butov)-0.2;
		 } elseif (($this->focus_input=='calcrabica-stolb_beton') ){
			 $this->stolb_glubina='zaglub_none';
			 $this->stolb_butov='butov_none';
			  //определяем высоту столба как высоту забора + высота бетонирования
			 $this->stolb_height=$this->height + $this->getwork($this->stolb_beton)-0.2;
		 }
		 

		 
	 return;	
	}
	
	public function calcInit1() { // устанавливаем значения при первом открытии формы
	    $this->stolb_nado=1;				//нужны ли столбы
	    $this->laga_nado=1;				//нужны ли лаги	
	    $this->rabica_nado=1;			//только рабица без металлического каркаса
	    $this->rabota_nado=1;			//только работы по установке забора

		$this->stolb_len=2.5;																		// расстояние между столбами
		$this->stolb_razmer='stolb_60*30';	
		$this->stolb_razmer_text=$this->readElements(8000,1);						// type =1  выбор столбов
		$this->stolb_glubina='zaglub1_2';														// при инициализации заглубляем столб на 1.2
		$this->stolb_height=$this->height + $this->getwork($this->stolb_glubina)-0.2;
		$this->stolb_glubina_text=$this->readWorks(6);	 							// type=6 заглубление столбов
		$this->stolb_count=ceil ($this->len/$this->stolb_len);						// расстояние между столбами округляем в большую
		$this->stolb_count=0;																		
		$this->stolb_krishka=$this->stolb_count;											// количество крышек
		$this->stolb_grunt='grunt_none';				
		$this->stolb_okraska='color_none';			
		$this->stolb_butov='butov_none';					
		$this->stolb_beton='beton_none';			
		$this->stolb_grunt_text=$this->readElements(9,0);							// грунтуем
		$this->stolb_okraska_text=$this->readElements(12,1);					// окрашиваем
		$this->stolb_butov_text=$this->readWorks(4);								// бутование
		$this->stolb_beton_text=$this->readWorks(5);								// бетонирование
		$this->stolb_ukosin=0;
		$this->stolb_otkos=0;
		$this->stolb_ukosin_kol=$this->stolb_ukosin*$this->stolb_count;	//общее число укосин

		return;
	}
	
	public function calcStep1() { 	// расчет после изменения любого из значений формы
												
		$this->stolb_razmer_text=$this->readElements(1,0);						// type =1  выбор столбов
		$this->stolb_glubina_text=$this->readWorks(6);	 							// type=6 заглубление столбов
		$this->stolb_grunt_text=$this->readElements(9,0);							// грунтуем
		$this->stolb_okraska_text=$this->readElements(12,1);					// окрашиваем
		$this->stolb_butov_text=$this->readWorks(4);								// бутование
		$this->stolb_beton_text=$this->readWorks(5);								// бетонирование
		$this->calc_stolb();
		
			
		//если выбрано покрытие 3 в 1  то выбор краски меняем на выбор цвета
		if ($this->stolb_grunt=='pokritie_3v1') {
			$this->stolb_okraska_text=$this->readElements(10,1)+$this->readElements(10,2);				
		}
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
				$inputdata->type_calc="rabica";
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
				
				// ########  сохраняем тип : профлист, рабица, штакет...
				
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s");  $smeta->zabortype='rabica';
					$smeta->attr   = 'rabica'; 
					$smeta->type  = $s; 
					$smeta->kol  = 0;
					$smeta->ed='';
					$smeta->price =  0;
					$smeta->table = 'info';	//признак информационной записи
					$smeta->insert(false); 

				// END ######## несьемная стяжка на калитку	
				
				// ######## количество столбов в смету
				if ($this->stolb_nado) {
					
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
				
				$smeta->attr   = $this->stolb_razmer;
				$smeta->type  = '( Длина столба: '.$this->stolb_height.' м, '.$this->getelement_name($this->stolb_grunt).', '.$this->getelement_name($this->stolb_okraska).')';
				$smeta->kol    = $this->stolb_count;
				$smeta->ed='шт.';
				// стоимость столба это цена за метр умноженные на высоту столба
				$smeta->price =  $this->getelement_price($this->stolb_razmer)*$this->stolb_height;
				
				$dop='';
				
				//добавляем в цену столба стоимость грунтовки
				if ($this->stolb_grunt != 'grunt_none') {
					$smeta->price = $smeta->price + $this->getelement_price($this->stolb_grunt)*$this->stolb_height;
				}
				//добавляем в цену столба стоимость покраски
				if ($this->stolb_okraska != 'kraska_none') {
					$smeta->price = $smeta->price + $this->getelement_price($this->stolb_okraska)*$this->stolb_height;
				}				
				
				$smeta->table = 'e1';	//признак материалов. признак dop учитывается для установки по умолчанию полей например калитки
				
				$smeta->insert(false); 
			}		
				// END ######## количество столбов в смету
		
				
			
				// ################# материалы по заглублению столба в смету
				if (($this->rabota_nado) &&($this->stolb_nado)) {
					
				if ( $this->stolb_glubina == 'zaglub_none') { // если не заглубление значит бутование или бетонирование
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					if ( $this->stolb_butov  != 'butov_none' )  { $smeta->attr=$this->stolb_butov; };
					if ( $this->stolb_beton  != 'beton_none' )  { $smeta->attr=$this->stolb_beton; };
					if ( $this->stolb_glubina != 'zaglub_none')  { $smeta->attr=$this->stolb_glubina; };
					
					
					$smeta->type  = '';	// 'Количество столбов: '.$this->stolb_count;
					$smeta->kol    = $this->stolb_count;
					$smeta->ed='ед.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					
					//выделяем величину заглубления в dop
								
					$smeta->table = 'e2';						//признак работы
					$smeta->insert(false); 		
				}
				}
				// END ################# заглубление столба в смету

							
						
				// ################# работы по заглубление столба в смету
				if (($this->rabota_nado) &&($this->stolb_nado)) {
				
				$glubina=0;
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
				
				if ( $this->stolb_butov  != 'butov_none' )  { $smeta->attr=$this->stolb_butov; };
				if ( $this->stolb_beton  != 'beton_none' )  { $smeta->attr=$this->stolb_beton; };
				if ( $this->stolb_glubina != 'zaglub_none')  { $smeta->attr=$this->stolb_glubina; };
				
				$glubina=$smeta->attr;
				
			
				// если это бутование или бетонирование, то записываем в смету работ. если только заглубление, то учитываем в стоимости работ за погонный метр
			
				if (substr($glubina,0,3) != 'zag') {
					$smeta->type  =  'Количество столбов: '.$this->stolb_count;
					$smeta->kol    = $this->stolb_count;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w2';					//признак работы
					$smeta->insert(false); 		
				}
				}
				// END ################# заглубление столба в смету
			
			

				
				// ############# крышек в смету
			 if ($this->stolb_nado) {
				$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
			
				
				$smeta->attr = ( $this->stolb_razmer=='stolb_60_60') ? 'krishka_stolb_60_60' : 'krishka_stolb_80_80';
				
				$smeta->type  ='';// 'Размер '.( $this->stolb_razmer=='stolb_60_60') ? '60х60' : '80х80';
				$smeta->kol    = $this->stolb_count;
				$smeta->ed='шт.';
				$smeta->price =  $this->getelement_price($smeta->attr);
				$smeta->table = 'e3';	//признак материалов
				$smeta->insert(false); 			
			 }
				// END ############# крышек в смету
				
				
				
				
				// ############# укосин в смету
				 if ($this->stolb_nado && ($this->stolb_ukosin_kol>0)) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = 'ukosina';
					$smeta->type  = ' (На один столб: '.$this->stolb_ukosin.' укосин) ';
					$smeta->kol    = $this->stolb_ukosin_kol;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e4';	//признак материалов
					$smeta->insert(false); 	
				 }
				// END ############# укосин в смету
				
				// ########### работы по установке укосин в смету
				if  (($this->stolb_ukosin_kol >0)  &&  ($this->stolb_nado)){
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = 'ukosina';
					$smeta->type  = ''; 
					$smeta->kol    = $this->stolb_ukosin_kol;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w5';	//признак материалов
					$smeta->insert(false); 	
				};
				// END ########### укосина в смету
				

			
				
				// ############# откосов в смету
				if  (($this->stolb_otkos >0) &&  ($this->stolb_nado)) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = 'otkos';
					$smeta->type  = ' ';
					$smeta->kol    = $this->stolb_otkos;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					
					
					//добавляем в цену столба стоимость грунтовки
					if ($this->stolb_grunt != 'grunt_none') {
						$smeta->price = $smeta->price + $this->getelement_price($this->stolb_grunt)*$this->getelement($smeta->attr);
					}
					//добавляем в цену столба стоимость покраски
					if ($this->stolb_okraska != 'kraska_none') {
						$smeta->price = $smeta->price + $this->getelement_price($this->stolb_okraska)*$this->getelement($smeta->attr);
					}		

				
					$smeta->table = 'e9';	//признак материалов
					$smeta->insert(false); 	
				};
				// END ############# откосов в смету				
				
				// ########### работы по установке откоса в смету
				if  (($this->stolb_otkos >0) &&  ($this->stolb_nado)) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = 'otkos_ustanovka';
					$smeta->type  = ''; //$this->getelement_name($this->planka);
					$smeta->kol    = $this->stolb_otkos;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w6';	//признак материалов
					$smeta->insert(false); 	
				};
				// END ########### откос в смету
				
				
				// ############ лаг в смету
				if ($this->laga_nado) {
					
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = $this->laga_razmer;
					$smeta->type  = '( По высоте '.$this->laga_kol.' лаг(и), длина лаги: 3м, '.$this->getelement_name($this->stolb_grunt).', '.$this->getelement_name($this->stolb_okraska).')';;
					
					$smeta->kol    = $this->laga_vsego;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					
					//добавляем в цену лаг стоимость грунтовки
					if ($this->stolb_grunt != 'grunt_none') {
						$smeta->price = $smeta->price + $this->getelement_price($this->stolb_grunt)*3;

					}
					//добавляем в цену лаг стоимость покраски
					if ($this->stolb_okraska != 'kraska_none')  { 
						$smeta->price = $smeta->price + $this->getelement_price($this->stolb_okraska)*3;

					}		
					
					
					$smeta->table = 'e5';	//признак материалов
					$smeta->insert(false); 	
				}
				// END  ############ лаг в смету
				
				
				// ############ профлист в смету
				if ($this->rabica_nado) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = $this->proflist_type;
					$smeta->type  = '( Цвет: '.$this->getelement_name($this->proflist_color).')';
					$smeta->kol    = $this->proflist_count;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr)*$this->height;
					$smeta->table = 'e6';	//признак материалов
					$smeta->insert(false); 					
					// END ############ профлист в смету
					
					
					// ########### планка в смету
					
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = $this->planka;
					$smeta->type  = ''; //$this->getelement_name($this->planka);
					$smeta->kol    = $this->planka_count;
					$smeta->ed='м';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e7';	//признак материалов
					$smeta->insert(false); 		
				}
				// END ########### планка в смету
				
				
				
				// ########### работы по установке планки в смету

					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = $this->planka;
					$smeta->type  = ''; //$this->getelement_name($this->planka);
					$smeta->kol    = $this->planka_count;
					$smeta->ed='';
					$smeta->price =  $this->getwork_price($smeta->attr);
					$smeta->table = 'w3';	//признак материалов
					$smeta->insert(false); 	
		
				// END ########### планка в смету

				
				
				// ############## саморезы в смету
				if ($this->rabica_nado) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';
					
					$smeta->attr   = $this->samorez_type;
					$smeta->type  = ' ( Цвет: '.$this->getelement_name($this->samorez_color).')';
					$smeta->kol    = $this->samorez_count;
					$smeta->ed='шт.';
					$smeta->price =  $this->getelement_price($smeta->attr);
					$smeta->table = 'e8';	//признак материалов
					$smeta->insert(false); 
				};
				// END ############## саморезы в смету
				
				
				// ########### работы по установке метра забора базовая стоимость за метр плюс стоимость за каждую лагу
				if ($this->rabota_nado) {
					$smeta=new Smeta(); $smeta->user_id=$c; 	$smeta->smeta_id=$s;  $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype='rabica';


					$smeta->type  =  'h='.$this->height.'м, l='.$this->len.'м, '.$this->laga_kol.' лаг(и) '.$this->getelement_name($this->stolb_razmer).' загл. '.($this->stolb_height+0.2-$this->height);				
					$smeta->kol    = $this->len;
					$smeta->ed='';
			
					$glubina=substr($glubina,strlen($glubina)-3,3);						//определяем глубину будь то бутование заглубление или бетонирование					
					$zaglub   = 'zaglub_'.$glubina;												//величина заглубления столбов
					$zaglub_price =  $this->getwork_price($zaglub);					//стоимость заглубления забора
					
					if ($this->laga_razmer=='laga_40_20')  								//начинаем формировать строку атрибута для поиска в базе цены за данный тип работ
						$laga='40*20' ;
					else $laga='60*30';		
					
					if ($this->stolb_razmer=='stolb_80_80')  
						$stolb='80*80' ;
					else $stolb='60*60';
					
					$findpart='prof_stolb_'.$laga.'_'.$stolb.'-';			
					$stolb_price=$this->findprice($findpart,$this->height);

					
					$findpart='prof_list_'.$laga.'_'.$stolb.'-';			
					$list_price=$this->findprice($findpart,$this->height);					
					
					$findpart='prof_laga_'.$laga.'_'.$stolb;		
					$laga_price=$this->getwork_price($findpart);					
					$laga_price=$laga_price*($this->laga_kol);
					
					$smeta->attr='proflist_za_metr';
					$smeta->price=$zaglub_price + $stolb_price + $list_price + $laga_price;	
									
					$smeta->table = 'w4';					//признак работ
					$smeta->insert(false); 		
				}
				// END ########### планка в смету				
				
			//************************ КОНЕЦ СОХРАНЯЕМ СМЕТУ НА ПРОФЛИСТ *********************	
			
				$smeta->readSmeta($c,$s);
				
			} else {
			
					$this->calc_step=1;
					$this->calcStep1();
			}	
	
	
	
	
}
	
}

?>