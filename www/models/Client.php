<?php

namespace app\models;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;


class Client extends ActiveRecord {
	public $focus_input;
	public $work_status_text=['1'=>'расчет','2'=>'думает','3'=>'подписан договор', '4'=>'в работе', '5'=>'завершено','6'=>'отменено'];	
	public $date_base_text;				//прописью дата внесения в базу
	public $date_dogovor_text; 		// прописью дата заключения договора
	public $date_start_text;				//прописью дата начала работ
	public $date_end_text;				//прописью дата завершения работ

    public static function tableName()
    {
        return '{{clients}}';
    }
	
	
public function attributeLabels()
    {
        return [
		
			'id_client'=>'Клиент', 
			'dogovor'=>'Договор',
			'name'=> 'Имя',
			'tel'=>'Тел', 
			'address'=>'Адрес',
			'passport'=>'Паспортные данные',
			'email'=>'E-Mail',
			'w'=>'Общая длина забора',		
			'h'=>'Высота забора',
			'comment'=>'Комментарий',
			'datetime'=>'Дата',
			'summa'=>'Итоговая сумма',
			'datedogovor'=>'Дата договора',
			'datestart'=>'Дата начала',
			'dateend'=>'Дата завершения',
			'avans'=>'Сумма аванса',
			'work_status'=>'Статус работ'
        ];
    }
	
		
	
	public function rules()
    {
        return [
			[['id_client'], 'integer'],
			[['h', 'w','summa','avans'], 'double'],
            [['focus_input','dogovor','name','tel', 'address', 'email','comment','arhiv','passport','datedogovor','datestart','dateend','work_status'], 'string',]
        ];
    }
	
	
	 public function getLastId () {
		 $last_id = Client::find()->select(['id_client'])->orderBy(['id_client'=>SORT_DESC])->asArray()->one();
	   return $last_id['id_client'];	 
	 }
	 
	 public function readClient()

    {
		
		$client = Client::find()->select(['id', 'id_client', 'dogovor','name','tel', 'address', 'email','comment','datetime'])->orderBy('id')->asArray()->all();
		return $client;
    }
	
	 public function readClientId($id_client)

    {
		
		$client = Client::find()->where(['id_client' => $id_client])->one();
		return $client;
    }
	
	public function delete_client_id($id_client) 
	
	{
		Client::deleteAll(['id_client' => $id_client]);
		return ;
	}
	
    public function search($params, $where = null)
    {
        $query = Client::find()->where($where)->orderBy(['Id'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
            ],

            'sort' => [
                'attributes' => ['dogovor', 'name', 'tel', 'email','summa','datestart','dataend','work_status',
                    'dogovor' => [
                        'asc' => ['dogovor' => SORT_ASC],
                        'desc' => ['dogovor' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['name' => SORT_ASC],
                        'desc' => ['name' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'dogovor', $this->dogovor]);
		$query->andFilterWhere(['like', 'name', $this->name,false]);
        $query->andFilterWhere(['like', 'tel', $this->tel]);
		$query->andFilterWhere(['like', 'summa', $this->summa]);
		$query->andFilterWhere(['like', 'datestart', $this->datestart]);
		$query->andFilterWhere(['like', 'dateend', $this->dateend]);
		$query->andFilterWhere(['like', 'work_status', $this->work_status]);		
		
        return $dataProvider;
    }
	
	
	
	
	
	public function init() {
		$d=substr($this->datetime,0,10);	//выделяем дату
		$d=$d[0].$d[1].$d[2].$d[3].$d[5].$d[6].$d[8].$d[9];
		$this->date_base_text=$this->makeDate($d);;				
		$this->date_dogovor_text=$this->makeDate($this->datedogovor); 		
		$this->date_start_text=$this->makeDate($this->datestart);				
		$this->date_end_text=$this->makeDate($this->dateend);				
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