<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;


class AktBegin extends FuncZabor {

	public $keys=["dogovor_number","client_name"];
	
	public $page;
	public $email;
	public $dogovor_number;
	public $client_name;
	
	
	
    public static function tableName()
    {
        return '{{smetatxt}}';
    }	


	public function init($client) {
		$this->email=$client->email;
		$this->dogovor_number=$client->dogovor;
		$this->client_name=$client->name;
	}
	
	
	public function getBodyAndMake($id_client) {
		
		$url= \Yii::$app->request->getHostInfo().\Yii::$app->request->getBaseUrl(true).'/index.php?r=site/aktbegintxt';


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