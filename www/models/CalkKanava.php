<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\BaseYii;
use app\models\CalcSmeta;

class CalcOtkatnieVorota  extends ActiveRecord {
	
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
	

	
}

?>