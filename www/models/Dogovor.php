<?php
namespace app\Models;
use yii\db\ActiveRecord;
use app\Models\Client;

class Dogovor extends ActiveRecord {
	
	public $keys=["dogovor_price_text", "dogovor_avans_text", "dogovor_ostatok_text", "dogovor_number", "dogovor_date",
	"dogovor_start", "dogovor_end","dogovor_price","dogovor_avans","avans_percent", "dogovor_ostatok", "ostatok_percent",
	"client_name","client_passport1line","client_passport2line","dogovor_address","dogovor_tel"];
	
	public $page;
	public $email;
	public $dogovor_number;
	public $dogovor_start;
	public $dogovor_end;
	public $dogovor_date;	
	public $dogovor_price;
	public $dogovor_price_text;
	public $dogovor_avans;
	public $dogovor_avans_text;
	public $dogovor_ostatok;
	public $dogovor_ostatok_text;
	public $client_name;
	public $client_passport1line;
	public $client_passport2line;
	public $dogovor_address;
	public $dogovor_tel;
	public $avans_percent;
	public $ostatok_percent;
	
	
    public static function tableName()
    {
        return '{{smetatxt}}';
    }
	
	public function attributeLabels()
    {
        return [
			'id_client'=>'Номер клиента',
			'ew'=>'Тип работ',
			'smeta_id'=>'Номер сметы',
			'poz' =>'Поз',
			'name'=>'Наименование',
			'kol'=>'Количество',
			'ed'=>'Ед.изм.',
			'price'=>'Стоимость',
			'summa'=>'Сумма'
        ];
    }	
		
	
	
	
	public function rules()
    {
        return [
            [['id_client', 'ew', 'smeta_id','name','kol','price','summa'], 'required'],
			[['ew', 'name','ed','page'], 'string'],
			[['kol','price','summa'],'double'],
			[['id_client','smeta_id','poz'],'integer']		
        ];
    }
	
	public function init($client) {
			
			$this->email=$client->email;
			$d=substr($client->datetime,0,10); //выделяем дату
			$d=$d[0].$d[1].$d[2].$d[3].$d[5].$d[6].$d[8].$d[9];
			
			$this->dogovor_number=$client->dogovor;
			$this->dogovor_date=$this->makeDate($client->datedogovor);
			$this->dogovor_start=$this->makeDate($client->datestart);
			$this->dogovor_end =$this->makeDate($client->dateend);
			$this->dogovor_price=$client->summa;	
			$this->dogovor_avans=$client->avans;
			$this->client_name=$client->name;
			$this->client_passport1line=$client->passport;
			$this->client_passport2line=$this->newLine( $client->passport,5);
			$this->dogovor_address=$this->newLine($client->address,3);
			$this->dogovor_tel=$client->tel;		
			$this->avans_percent=round(($this->dogovor_price-$this->dogovor_avans)*100/$this->dogovor_price,2);
			$this->ostatok_percent=100-$this->avans_percent;
			$this->dogovor_ostatok=$this->dogovor_price-$this->dogovor_avans;
			
			$this->dogovor_price_text=$this->num2str($this->dogovor_price);
			$this->dogovor_avans_text=$this->num2str($this->dogovor_avans);
			$this->dogovor_ostatok_text=$this->num2str($this->dogovor_ostatok);

			$this->dogovor_price=number_format($client->summa ,2,'.',' ');	
			$this->dogovor_avans=number_format($client->avans ,2,'.',' ');
			$this->dogovor_ostatok=number_format($this->dogovor_ostatok ,2,'.',' ');
	}
	
	
	public function getBodyAndMake($id_client) {
		
		$url= \Yii::$app->request->getHostInfo().\Yii::$app->request->getBaseUrl(true).'/index.php?r=site/dogovortxt';

		//читаем в переменную выделяем то, что обрамлено тегами <!-- START MAIL--> ... <!-- END MAIL-->
		$this->page=file_get_contents($url);
				
		$poz_start=strpos($this->page,'<!--START MAIL-->');
		$poz_end=strpos($this->page,'<!--END MAIL-->');
		$poz_end=$poz_end-$poz_start+15;
		$this->page=substr($this->page, $poz_start, $poz_end);
		
		foreach ($this->keys as $key) {
			//найти в page  и произвести замену значением key		
			$this->page=str_ireplace('{'.$key.'}', $this->$key, $this->page);
		}
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
	
		

function summaSmetaTxt ($id_client) {
	$sql="SELECT SUM(summa) as itogo FROM smetatxt where id_client=".$id_client;
	$command =\Yii::$app->db->createCommand($sql);
	$sum = $command->queryScalar();

	return $sum;		
}

function makeDate($d)	 {
	// запись в таблице 20170201 - представить в удобочитаемом виде
		
	$m=['01'=>'января', '02'=>'февраля','03'=>'марта','04'=>'апреля','05'=>'мая', '06'=>'июня','07'=>'июля','08'=>'августа',
	'09'=>'сентября','10'=>'октября','11'=>'ноября','12'=>'декабря'];	
	
  
return $d[6].$d[7].' '.$m[$d[4].$d[5]].' '.$d[0].$d[1].$d[2].$d[3];
	
}

public function newLine($str,$ns) { //функция которая вставляет перевод строки в str на $ns пробеле
 $len=strlen($str);
 $kol=0;
  for ($i=0;$i<$len;$i++) {
	  if ($str[$i]===' ') { 
		$kol++; 
	    if ($kol==$ns) { break; }//выпрыгивем с цикла если наситали нужное число пробелов
	  }
  }
 return ( $kol != 0) ? substr($str,0,$i).'<br/>'.substr($str,$i,$len): $str;
}
	
}

?>