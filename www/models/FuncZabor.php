<?php

/*
  ******************************** СЮДА ВЫНОСИМ ВСЕ ОБЩИЕ ДЛЯ ЭЛЕМЕНТОВ ЗАБОРА ФУНКЦИИ ******************************
*/

namespace app\models;

use app\models\Yii;
use yii\db\ActiveRecord;


class FuncZabor  extends ActiveRecord {
	
	
	
	public  $zabor_elements=[
			'proflist'=>'Забор из профнастила',
			'shtaket'=>'Забор из штакета', 
			'kalitka'=>'Калитка', 
			'vorota'=>'Распашные ворота',
			'otkatnievorota'=>'Откатные ворота',
			'fundament'=>'Ленточный фундамент',
			'parkovka'=>'Парковка',
			'svai'=>'Сваи',
			'kanava'=>'Вьезд через канаву',
			'transport'=>'Транспорт'
	];	
	
	
		public $document_text=[
		'readsmetatxt'=>'Тех задание',
		'dogovor'=>'Договор', 
		'aktbegin'=>'Акт начала работ', 
		'aktend'=>'Акт выполненных работ',
		'editsmeta'=>'Правка сметы',
		'texsmeta'=>'Техническая смета'
	];
	
	
	
	public function delSmetaInputForm ($c,$s ){
		\Yii::$app->db->createCommand()->delete('smeta', ['user_id'=>$c, 'smeta_id' => $s]) ->execute();
		\Yii::$app->db->createCommand()->delete('input_form', ['user_id'=>$c, 'smeta_id' => $s]) ->execute();			
		
	}

	public function restore_form($c,$s ) { // восстановить форму из сметы
	
		$formdata=InputForm::find()->select(['input_attr','input_data'])->where(['user_id' => $c,'smeta_id' => $s])->all();

		foreach ($formdata as $inputform) {
			$varname=$inputform->input_attr;
			$this->$varname=$inputform->input_data;
		}
		$this->delSmetaInputForm ($c,$s );
	 
	}
	
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
	  
	    public function readSmeta($id_client,$zabor_type)

    {
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed','type','kol','price'])->
		where(['user_id' => $id_client,'zabortype'=>$zabor_type])->orderby('table')->asArray()->all();

		return $smeta;
    }
	

	public function getSmetaAttr($id_client, $attr) 
	{
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed', 'type','kol','price'])->
		where(['user_id' => $id_client, 'attr' => $attr])->asArray()->one();
		
		return $smeta;
	}
	
	public function getSmetaTable($id_client, $attr) 
	{
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed','type','kol','price'])->
		where(['user_id' => $id_client, 'attr' => $attr])->asArray()->one();
		
		return $smeta;
	}
	

	
	public function summaSmeta($id_client)

    {	
		//'user_id','smeta_id','attr','table','type','kol','price'
		
		$sql="SELECT SUM(summa) as itogo FROM (SELECT (kol*price) as summa FROM smeta WHERE user_id = '".$id_client."' ) as sum_table";
		$command =\Yii::$app->db->createCommand($sql);
		$sum = $command->queryScalar();

		return $sum;
    }
	

	function summaSmetaTxt ($id_client) {
		$sql="SELECT SUM(summa) as itogo FROM smetatxt where id_client=".$id_client;
		$command =\Yii::$app->db->createCommand($sql);
		$sum = $command->queryScalar();

		return $sum;		
	}
	
	
	public function getArrMinus( $id_client) {
		//формируем массив название минусуемого материала и количество, затем при показе сметы прогоняем все через минуса и если надо минусуем
		$smeta = Smeta::find()->select(['attr','zabortype', 'type','kol'])->where(['user_id' => $id_client, 'table' => '-'])->asArray()->all();

		return $smeta;
	}
		
	
	public function readSmetaForSmetaId($id_client,$zabor_type,$smeta_id)
    {
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed','type','kol','price'])->
		where(['user_id' => $id_client, 'zabortype'=>$zabor_type, 'smeta_id'=>$smeta_id])->orderby('table')->asArray()->all();

		return $smeta;
    }

	
	public function delete_smeta($user_id, $smeta_id) 
	
	{
		Smeta::deleteAll(['user_id' => $user_id, 'smeta_id' => $smeta_id]);
		return ;
	}
	
	
	
	public function findprice($attr,$h) {
		$h=$h*100;
		$element = Rabota_Type::find()->select(['attr', 'price'])->andWhere(['like','attr',$attr])->
		andWhere('attr >="'.($attr.$h).'"')->orderBy('attr')->limit(1)->asArray()->all();
		return $element['0']['price'];
	}


	public function getkalitka($attr,$h) {
		$h=$h*100;

		$element = Element_Type::find()->select(['id','name', 'kol','attr','price'])->andWhere(['like','attr',$attr])->
		andWhere('attr >="'.($attr.$h).'"')->orderBy('attr')->limit(1)->asArray()->all();
		
		return $element['0'];
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
		



	
	public function minusovanieMaterialov ($id_client) {
		
	if (true) { 
			
			// ***************************************************************************************************
			//**********  мясо по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//**********  мясо по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//**********  мясо по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//**********  мясо по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//**********  мясо по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//**********  мясо по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//**********  мясо по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			// ***************************************************************************************************
			
		// чистим смету от текущих минусов, т.к. пришли с нового расчета и формируем новый блок минусов
		
		 \Yii::$app->db->createCommand()->delete('smeta', ['user_id'=>$id_client, 'table' =>'-']) ->execute();
		 
		  $zabortypes=['proflist','shtaket'];
		  
		  foreach ($zabortypes as $zabortype) {
			
		
		 
		  $smeta=new Smeta();

		  $minusa=$smeta->getSmetaTable($id_client, 'minus') ;
		  
		  $s=$smeta->getSmetaAttrTT($id_client, $zabortype,$zabortype,'info')['type']; // в type  зашифрован номер сметы для $zabortype
		  
	  
		  if ($s!=null) { // ищем минуса в смете  и создаем  их как минусуемые материалы, которые в смете будут вычитаться

		  $inputform=new InputForm();
		
		
		  //считаем сколько калиток, ворот для данного $type настила и формируем записи минуса, которые в смете будут учитываться
		 $kalitok_count=0;
		 $vorota_count=0;

		 foreach ($minusa as $minus) {

			if (($minus['zabortype']=='kalitka') && ($minus['type']==$zabortype)) {
				 $kalitok_count+=1;
			}
			
			if ((($minus['zabortype']=='vorota')  || ($minus['zabortype']=='otkatnievorota')) && ($minus['type']==$zabortype) ) {
				 $vorota_count+=1;
			}				

			
		  } // foreach по всем table=minus
		
		 $minus_stolb   	= $vorota_count*2+$kalitok_count;  
		 $minus_krichki		= $minus_stolb;
		 $ukosina_minus	= $inputform->getAttr($id_client,'stolb_ukosin',$zabortype)['input_data']*$minus_stolb; // количество укосин * на столб на количество столбов
		 $laga_minus		= $vorota_count*(($kalitok_count>0) ? 2:1.5)*$inputform->getAttr($id_client,'laga_kol',$zabortype)['input_data'];
		 $proflist_minus	= $vorota_count;
		 $sum_minus=0;
		 
		 //забрасываем минуса в смету (если в будущем будет тормозить то в отдельную смету бросать минуса)
 
		//определяем тип столба
		$stolb_type= $inputform->getAttr($id_client,'stolb_razmer',$zabortype)['input_data'];
		
		if ($inputform->getAttr($id_client,'stolb_nado',$zabortype)['input_data']>0) {
			//минусуем столбы 
			$smeta=new Smeta(); $smeta->user_id=$id_client; $smeta->smeta_id=$s; $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype=$zabortype;				
			$smeta->attr   =$stolb_type;
			$smeta->type  = 'stolb_razmer'; 
			$smeta->kol    = -1*$minus_stolb;
			$smeta->ed='';
			$smeta->price =  $smeta->getSmetaPrice($id_client, $smeta->attr,$zabortype);
			$smeta->table = '-';	//признак минусования в смете, однако в смете используем сложение
			$smeta->insert(false); 	
			$sum_minus+=$smeta->kol*$smeta->price;

			//минусуем крышки 
			$smeta=new Smeta(); $smeta->user_id=$id_client; $smeta->smeta_id=$s; $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype=$zabortype;				
			$smeta->attr   ='krishka_'.$stolb_type;
			$smeta->type  = 'stolb_krishka'; 
			$smeta->kol    = -1*$minus_stolb;
			$smeta->ed='';
			$smeta->price =  $smeta->getSmetaPrice($id_client, $smeta->attr,$zabortype);
			$smeta->table = '-';	//признак минусования в смете, однако в смете используем сложение
			$smeta->insert(false); 		
			$sum_minus+=$smeta->kol*$smeta->price;
			
			//минусуем укосины
			if ($inputform->getAttr($id_client,'ukosin_count',$zabortype)['input_data']>0) {
				$smeta=new Smeta(); $smeta->user_id=$id_client; $smeta->smeta_id=$s; $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype=$zabortype;				
				$smeta->attr   ='ukosina';
				$smeta->type  = 'stolb_ukosin'; 
				$smeta->kol    = -1*$ukosina_minus;
				$smeta->ed='';
				$smeta->price =  $smeta->getSmetaPrice($id_client, $smeta->attr,$zabortype);
				$smeta->table = '-';	//признак минусования в смете, однако в смете используем сложение
				$smeta->insert(false); 		
				$sum_minus+=$smeta->kol*$smeta->price;
			}
			if ($inputform->getAttr($id_client,'otkos_count',$zabortype)['input_data']>0) {
				//минусуем откосы  
				$smeta=new Smeta(); $smeta->user_id=$id_client; $smeta->smeta_id=$s; $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype=$zabortype;				
				$smeta->attr   ='ukosina';
				$smeta->type  = 'stolb_ukosin'; 
				$smeta->kol    = -1*$ukosina_minus;
				$smeta->ed='';
				$smeta->price =  $smeta->getSmetaPrice($id_client, $smeta->attr,$zabortype);
				$smeta->table = '-';	//признак минусования в смете, однако в смете используем сложение
				$smeta->insert(false); 		
				$sum_minus+=$smeta->kol*$smeta->price;			
			}	
		}
		
		if ($inputform->getAttr($id_client,'laga_nado',$zabortype)['input_data']>0) {
			//определяем тип лаг
			$laga_type= $inputform->getAttr($id_client,'laga_razmer',$zabortype)['input_data'];
			
			//минусуем лаги 
			$smeta=new Smeta(); $smeta->user_id=$id_client; $smeta->smeta_id=$s; $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype=$zabortype;				
			$smeta->attr   =$laga_type;
			$smeta->type  = 'laga_razmer'; 
			$smeta->kol    = -1*$laga_minus;
			$smeta->ed='';
			$smeta->price =  $smeta->getSmetaPrice($id_client, $smeta->attr,$zabortype);
			$smeta->table = '-';	//признак минусования в смете, однако в смете используем сложение
			$smeta->insert(false); 	
			$sum_minus+=$smeta->kol*$smeta->price;
		}
		//Внимание !!!!!!!!!  ниже расчеты зависящие от настила забора профлист, штакет
		
		if  (($zabortype=='proflist') && ($vorota_count>0) ){ //добавляем профлист на единицу ворот
			$smeta=new Smeta(); $smeta->user_id=$id_client; $smeta->smeta_id=$s; $smeta->datetime=date("Y-m-d H:i:s"); $smeta->zabortype=$zabortype;				
			$smeta->attr   =$inputform->getAttr($id_client,'proflist_type',$zabortype)['input_data'];
			$smeta->type  = 'proflist'; 
			$smeta->kol    = $proflist_minus; 
			$smeta->ed='';
			$smeta->price =  $smeta->getSmetaPrice($id_client, $smeta->attr,$zabortype);
			$smeta->table = '-';	//признак минусования в смете, однако в смете используем сложение
			$smeta->insert(false); 
			$sum_minus+=$smeta->kol*$smeta->price;			
		}	
		
		//минусуем сумму на странице клиента

			$zabor_tmp=new Zabor();
			$zabor_tmp=Zabor::find()->where(['id_client' => $id_client,'poz'=>$s])->one();
			$zabor_tmp->summa=$smeta->summaSmeta($id_client,  $s);
			$zabor_tmp->update(false);
		
			// ***************************************************************************************************
			//***КОНЕЦ   мяса по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//***КОНЕЦ   мяса по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//***КОНЕЦ   мяса по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//***КОНЕЦ   мяса по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение
			//***КОНЕЦ   мяса по пересчету столбов, лаг, крышек, работ  если добавлены калитки и ворота и требуется изменение		
			// ***************************************************************************************************
		 
		 }	//минусов по запросу из таблицы не найдено
		}//foreach
	}// конец мяса по пересчету
	}		
		
		
		
		
	


	/**
 * Возвращает сумму прописью
 * @author runcore
 * @uses morph(...)
 */
function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= $this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = $this->morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}
	
		

function makeDate($d)	 {
	// запись в таблице 20170201 - представить в удобочитаемом виде
		
	$m=['01'=>'января', '02'=>'февраля','03'=>'марта','04'=>'апреля','05'=>'мая', '06'=>'июня','07'=>'июля','08'=>'августа',
	'09'=>'сентября','10'=>'октября','11'=>'ноября','12'=>'декабря'];	
	
  
return $d[6].$d[7].' '.$m[$d[4].$d[5]].' '.$d[0].$d[1].$d[2].$d[3];
			
	
}	
		
		

		
}

?>