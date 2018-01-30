<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;


class AktEnd extends FuncZabor {
	
	public $keys=["dogovor_number", "dogovor_date",	"client_name"];	
	
	public $page;
	public $email;
	public $dogovor_number;
	public $dogovor_date;
	public $client_name;
	
	

    public static function tableName()
    {
        return '{{smetatxt}}';
    }	
		
		
		
	public function init($client) {
			$this->email=$client->email;
			$d=substr($client->datetime,0,10); //выделяем дату
			$d=$d[0].$d[1].$d[2].$d[3].$d[5].$d[6].$d[8].$d[9];
			
			$this->dogovor_number=$client->dogovor;
			$this->dogovor_date=$this->makeDate($d);	
			$this->client_name=$client->name;			
	}

	
	public function getBodyAndMake($id_client) {
		

		$url= \Yii::$app->request->getHostInfo().\Yii::$app->request->getBaseUrl(true).'/index.php?r=site/aktendtxt';

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
	



}
?>