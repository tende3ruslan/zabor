<?php

// к программе прилагается видео по схеме работы для облегчения поддержки
// пардон за толстый контроллер.

namespace app\controllers;
use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;



use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Element_Name;
use app\models\Element_Type;
use app\models\Karkas;
use app\models\Rabota_Name;
use app\models\Rabota_Type;

use app\models\Setelements;
use app\models\Setworkprice;
use app\models\Calc;
use app\models\FuncZabor; // функции по выборке и расчету забора
use app\models\CalcShtaket;
use app\models\CalcProflist;

use app\models\CalcKalitka;
use app\models\CalcVorota;
use app\models\CalcOtkatnieVorota;
use app\models\CalcFundament;
use app\models\CalcParkovka;
use app\models\CalcSvai;
use app\models\CalcKanava;
use app\models\CalcTransport;
use app\models\CalcRabica;

use app\models\Smeta;
use app\models\PrintSmeta;
use app\models\GenerateSmeta;
use app\models\EditSmeta;
use app\models\SendSmeta;
use app\models\InputForm;
use app\models\Client;
use app\models\Zabor;
use app\models\CalcSmeta;
use app\models\SmetaTxt;
use app\models\Dogovor;
use app\models\AktBegin;
use app\models\AktEnd;
use app\models\Settings;
use app\models\MailerForm;

class SiteController extends Controller
{
	
	const NS='app\models\\';																		//каталог php-моделей
	
	public  $zabor_elements=[
			'proflist'=>'Забор из профнастила',
			'shtaket'=>'Забор из штакета', 
			'kalitka'=>'Калитка', 
			'vorota'=>'Распашные ворота',
			'otkatnievorota'=>'Откатные ворота',
			'fundament'=>'Ленточный фундамент',
			'parkovka'=>'Парковка',
			'svai'=>'Сваи',
			'kanava'=>'Вьезд через канаву'
	];
	
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
		
	
		
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
			
			Yii::$app->session->open();			
			Yii::$app->session->set('test', 'Session active');
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
		Yii::$app->session->close();
        return $this->goHome();
    }


	
    public function actionContact()
    {
		
					
		/* ********** тестовый блок **************
		echo '*********************************';
		echo 'test         ='.Yii::$app->session->get('test').'<br/>';
		echo 'id_client   ='.Yii::$app->session->get('id_client').'<br/>';
		echo 'smeta_id ='.Yii::$app->session->get('smeta_id').'<br/>';
		echo '*********************************';

	    ************************************
		*/	
		

		
		if (!Yii::$app->user->isGuest) {
			
		$this->actionMergetable();
		
			
		$ret=['model' => $model,'passtrue'=>'1'];
		} else {
				$ret=['model' => $model,'passtrue'=>'0'];
		}
		
		return $this->render('contact', $ret);
		
    }	
	
	
	
	public function actionPrintsmeta() {  											//на странице calc нажата кнопка БАЗОВАЯ СМЕТА
	   if (!Yii::$app->user->isGuest) {	
		if (Yii::$app->request->get('id_client')) {
			 $id_client=Yii::$app->request->get('id_client');
		} else {
		 $id_client= Yii::$app->session->get('id_client');
		}	;	
		
	
		$printsmeta=new PrintSmeta();
		$client = Client::find()->where(['id_client' => $id_client])->one();
		$zabor = Zabor::find()->where(['id_client' => $id_client])->orderBy('smeta_id')->asArray()->all();
		$printsmeta->usermail=$client->email;
		$printsmeta->init();
		$printsmeta->validate();
		

		if (Yii::$app->request->get('minusa')=='show')  $printsmeta->smeta_type="minusa_show";
			else $printsmeta->smeta_type="minusa_hide";		
		
		$arr_minus=$printsmeta->getArrMinus($id_client);
		
		$gs_mail=new GenerateSmeta();
		$gs_mail->usermail=$client->email;
		$gs_mail->validate();
		$gs_mail->document="texsmeta";
		
		
		$ret=['client'=>$client,'zabor'=>$zabor, 'printsmeta'=>$printsmeta,'id_client'=>$id_client, 'arr_minus'=>$arr_minus,'gs_mail'=>$gs_mail];	
		return $this->render('printsmeta',$ret);
	   }
	}
	

	
	

    public function actionMailgo() {															// отправка почты пользователю с формы "смета клиента"
		
	  if (!Yii::$app->user->isGuest && \Yii::$app->request->post()) {	
	  
		if (Yii::$app->request->get('id_client')) {
			 $id_client=Yii::$app->request->get('id_client');
		} else {
			$id_client= Yii::$app->session->get('id_client');
		}
		
		 $document=next(\Yii::$app->request->post())["document"];
		 $email=next(\Yii::$app->request->post())["usermail"];
		
		if ($document==="readsmetatxt")  {$filename="sendsmeta"; $subj='Техническое задание';} 
		else
		{$filename=$document  ; $subj='Документы.';} ;
			
		$url= Yii::$app->request->getHostInfo().Yii::$app->request->getBaseUrl(true).'/index.php?r=site%2F'.$filename.'&id_client='.$id_client;		

		$page = file_get_contents($url);
		$poz_start=strpos($page,'<!--START MAIL-->');
		$poz_end=strpos($page,'<!--END MAIL-->');
		$page=substr($page, $poz_start, $poz_end);
		
		//читаем в переменную $content все что отдает send smeta и выделяем то, что обрамлено тегами <!-- START MAIL--> ... <!-- END MAIL-->

		$ss=new SendSmeta();
		$ss->from='zabor@inet2biz.ru';
		$ss->to=next(Yii::$app->request->post())['usermail'];
		$ss->subj='Установка забора.'.$subj;
		$ss->content=$page;
        $ss->sendMail();
		
		$ss->from='zabor@inet2biz.ru';
		$ss->to='rusmogbel@gmail.com';
		$ss->subj='отправлено:'.$subj;
		$ss->content='отправлено: '.$subj;
        $ss->sendMail();		
		
		return $this->render('mailgo');
	  } //if guest
	  
    }

	
	
	


	
	
	public function actionCalcsmeta() {   							//на странице CALC  нажата кнопка рассчитать или очистить  - переходим в нужный расчет
	
		
		
		$id_client= Yii::$app->session->get('id_client');
		
		if (($id_client=='NULL') || ($id_client=='')) {
			echo 'nekotorie problemi s funkcionirovaniem mexanizma sessij...';
			return;
		}
		
		$zabor=new Zabor();	
		$zabor->load(Yii::$app->request->post());
		
		// чистим расчет для текущей сметы при нажатии кнопки расчет
		\Yii::$app->db->createCommand()->delete('zabor', ['id_client'=>$id_client, 'smeta_id' => $zabor->smeta_id]) ->execute();

		
		if (Yii::$app->request->post('focus_input_zabor')=="clear_smeta")  { //сохраняем пустое поле - очищаем типа
			FuncZabor::delSmetaInputForm ($id_client,$zabor->smeta_id );
			$zabor->id_client=$id_client;
			$zabor->poz=$zabor->smeta_id;
			$zabor->h='';
			$zabor->l='';
			$zabor->type='';
			$zabor->summa='';
			$zabor->arhiv='';	

			$zabor->save();			
			FuncZabor::minusovanieMaterialov ($id_client);
		}
		
		else {
		Yii::$app->session->set('id_smeta',$zabor->smeta_id);
		
		//что по POST получили то и сохраняем, а предыдущее выше удалили
		$zabor->id_client=$id_client;
		$zabor->poz=$zabor->smeta_id;
		$zabor->summa='';
		$zabor->arhiv='go_to_calc';	
		$id_smeta=$zabor->smeta_id;
		$zabor->save();
		
	
		//выбираем нужный тип расчета
		$smeta=new Smeta();		
		
		if ($zabor->type=='calcproflist') { 
		
			$model = new CalcProflist(['scenario' => CalcProflist::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			$model->calcInit1();		
			$model->restore_form($zabor->id_client,$zabor->poz ); //восстанавливаем форму из input_form
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcproflist',$ret);			

		}		
		
		if ($zabor->type=='calcshtaket') { 
		
			$model = new CalcShtaket(['scenario' => CalcShtaket::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcshtaket',$ret);			

		}			
		
		if ($zabor->type=='kalitka') { 
			$model = new CalcKalitka(['scenario' => CalcKalitka::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			
		
			$input=new InputForm();			
			
			//устанавливаем значения по умолчанию для: 1- заглубление
			
			$but =$input->getAttr($id_client,'stolb_butov','')['input_data'];
			$bet =$input->getAttr($id_client,'stolb_beton','')['input_data'];
			$zag =$input->getAttr($id_client,'stolb_glubina','')['input_data'];
			
			
			$but =substr($but,-1); //берем последний символ глубины
			$bet=substr($bet,-1);
			$zag=substr($zag,-1);
			$glubina=(int) $but + (int) $bet + (int) $zag;
			
			if ($glubina>2) 
			{
				 $model->stolb_zaglub='zaglub_zabor_da'; 
			}	else {
				 $model->stolb_zaglub='zaglub_zabor_net'; 
			}				

			$model->nastil=$model->readSmetaForZaborType ($id_client);
			
			//устанавливаем значения по умолчанию для: 2-покраска 
			$model->stolb_grunt=$input->readAttr($id_client,-1,$model->nastil,'stolb_grunt');
			
			//устанавливаем значения по умолчанию для: 3-покраска 
			$model->stolb_okraska=$input->readAttr($id_client,-1,$model->nastil,'stolb_okraska');			

			//устанавливаем значения по умолчанию для: 4-покрытия 
			if ($model->nastil=='proflist')
			  $model->pokritie=$input->readAttr($id_client,-1,'','proflist_type');	
			if ($model->nastil=='shtaket')
			  $model->pokritie=$input->readAttr($id_client,-1,'','shtaket_type');	
			
			$model->calcInit1();		
					
			

			
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calckalitka',$ret);			

		}		
		
		if ($zabor->type=='vorota') { 
		
			$model = new CalcVorota(['scenario' => CalcVorota::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			
			$input=new InputForm();			
			
			//устанавливаем значения по умолчанию для: 1- заглубление
			//устанавливаем значения по умолчанию для: 1- заглубление
			
			$but =$input->getAttr($id_client,'stolb_butov','')['input_data'];
			$bet =$input->getAttr($id_client,'stolb_beton','')['input_data'];
			$zag =$input->getAttr($id_client,'stolb_glubina','')['input_data'];
			
			
			$but =substr($but,-1); //берем последний символ глубины
			$bet=substr($bet,-1);
			$zag=substr($zag,-1);
			$glubina=(int) $but + (int) $bet + (int) $zag;
			
			if ($glubina>2) 
			{
				 $model->stolb_zaglub='zaglub_zabor_da'; 
			}	else {
				 $model->stolb_zaglub='zaglub_zabor_net'; 
			}			
			
			//устанавливаем значения по умолчанию для: 2-покраска 
			$model->stolb_grunt=$input->readAttr($id_client,-1,'proflist','stolb_grunt');
			
			//устанавливаем значения по умолчанию для: 3-покраска 
			$model->stolb_okraska=$input->readAttr($id_client,-1,'proflist','stolb_okraska');			

			//устанавливаем значения по умолчанию для: 4-покрытия 
			$model->nastil=$model->readSmetaForZaborType ($id_client);
			if ($model->nastil=='proflist')
			  $model->pokritie=$input->readAttr($id_client,-1,'','proflist_type');	
			if ($model->nastil=='shtaket')
			  $model->pokritie=$input->readAttr($id_client,-1,'','shtaket_type');				
			
			
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcvorota',$ret);			

		}		
		
		if ($zabor->type=='otkatnievorota') { 
		
			$model = new CalcOtkatnieVorota(['scenario' => CalcOtkatnieVorota::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			
			//устанавливаем значения по умолчанию для: покрытия 
			$model->nastil=$model->readSmetaForZaborType ($id_client);
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcotkatnievorota',$ret);			

		}
		
		
		if ($zabor->type=='fundament') { 
		
			$model = new CalcFundament(['scenario' => CalcFundament::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcfundament',$ret);			

		}		

		if ($zabor->type=='parkovka') { 
		
			$model = new CalcParkovka(['scenario' => CalcParkovka::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcparkovka',$ret);			

		}		
		
		if ($zabor->type=='svai') { 
		
			$model = new CalcSvai(['scenario' => CalcSvai::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->diametr=$zabor->l;
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcsvai',$ret);			

		}				
		
		if ($zabor->type=='kanava') { 
		
			$model = new CalcKanava(['scenario' => CalcKanava::STEP1]);		
			$model->calc_step=1;
			$model->width=$zabor->h;
			$model->len=$zabor->l;
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calckanava',$ret);			

		}		

		if ($zabor->type=='transport') { 
		
			$model = new CalcTransport(['scenario' => CalcTransport::STEP1]);		
			$model->calc_step=1;
			$model->calcInit1();		
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calctransport',$ret);			

		}		

		if ($zabor->type=='calcrabica') { 
		
			$model = new CalcRabica(['scenario' => CalcRabica::STEP1]);		
			$model->calc_step=1;
			$model->height=$zabor->h;
			$model->len=$zabor->l;
			$model->calcInit1();		
			$model->restore_form($zabor->id_client,$zabor->poz); //восстанавливаем форму из input_form
			$ret=['model' => $model, 'smeta'=>$smeta, 'id_client' => $id_client, 'id_smeta' => $id_smeta, 'passtrue'=>1];
			return $this->render('calcrabica',$ret);			

		}				
		
		} 
		
		// если это не очистка
		

		$client  = Client::find()->where(['id_client' =>$id_client])->one();
		$zabor = Zabor::find()->where(['id_client'=>$id_client]) ->orderBy(['smeta_id' => ASC])->all();		

		$zabor_null=new Zabor();
		
		$ret=['client' => $client, 'zabor'=>$zabor,'zabor_null'=>$zabor_null, 'passtrue'=>1];	
		return $this->render('calc',$ret);

	} // конец calcSmeta - где выбираем нужный тип расчета
	
	
	
	
	
	
    public function actionCalc() { 																//страница клиента и списка работ

		if (!Yii::$app->user->isGuest) {
		
		
		
		$client=new Client();
		$zabor=new Zabor();
		$zabor_null=new Zabor();			
		
		
		$id_client = Yii::$app->session->get('id_client');
		$id_smeta = Yii::$app->session->get('id_smeta');
		
		
		if (Yii::$app->request->get('id_client')) {
			 $id_client=Yii::$app->request->get('id_client');
		}			
				
		if (($id_client=='')  ||  ($id_client==NULL) || ($id_client>$client->getLastId())) {
			$id_client=$client->getLastId();	
		}	
		
		
		$client=Client::find()->where(['id_client'=>$id_client])->one();
		
		
		if ((Yii::$app->request->get('h')) && (Yii::$app->request->get('l'))) { // вернулись из расчета и передали актуальную длину и высоту - сохраняем в таблицу забор

			 Yii::$app->db->createCommand()->update('zabor', [ 'h' => Yii::$app->request->get('h'), 'l' => Yii::$app->request->get('l'),
			 'arhiv' =>'end_calc'], '(id_client='. $id_client.') AND (smeta_id='.$id_smeta.')')->execute();		
				
		}
		
		if ($client->load(Yii::$app->request->post())) {

			 // ******************************** НАЖАТА КНОПКА "СОЗДАТЬ НОВЫЙ ЗАКАЗ"  ***********************
			
			if ($client->focus_input=='new_client') {  // если нажата кнопка "создать новый заказ
				$client->update();	//сохраняем текущего
				$client=new Client();
				$client->id_client=$client->getLastId()+1;
				$tmp=1500+$client->id_client;
				$client->dogovor=strval($tmp);
				$id_client=$client->id_client;
				$client->datetime=date("Y-m-d H:i:s");
				$client->insert();
				Yii::$app->session->set('id_client',$client->id_client);
			} //end new client
			
				 
			 if ($client->focus_input=='go_to_clear_client') {  // если нажата кнопка "очистить" для клиента
				$client->dogovor='';
				$client->name='';
				$client->tel='';
				$client->address='';
				$client->email='';
				$client->w=0;
				$client->h=0;
				$client->comment='';
				$client->arhiv='';
				$client->summa=0;
			 
			} // end go to calc		
			

			// ******************************** НАЖАТА КНОПКА "ВПРАВО ИЛИ ВЛЕВО"  ***********************
			if ($client->focus_input=='prev') {		 // листание клиента на предыдущего
				$id_client--;
				if ($id_client <=0) { $id_client=$client->getLastId(); }		
				Yii::$app->session->set('id_client',$id_client); 
			}
			
			
			if ($client->focus_input=='next') {		// листание клиента к следующему
				$id_client++;
				if ($id_client > $client->getLastId()) { $id_client=1; }
				Yii::$app->session->set('id_client',$id_client); 
			}					
			 

				// Если есть какой-то POST для формы клиента, то сохраняем данные клиента
				$client->datetime=date("Y-m-d H:i:s");
				$client->update();
							 
			if ($client->focus_input=='go_to_calc')  return; // если надо рассчитывать сохраняем и выпрыгиваем
			
		 } // конец условия при котором в форму получаются какие либо пост данные
		 
		
		// формируем карточку клиента на основе данных из таблиц client и zabor  по id_client и smeta_id
		$client  = Client::find()->where(['id_client' =>$id_client])->one();
		$zabor = Zabor::find()->where(['id_client'=>$id_client]) ->orderBy(['smeta_id' => ASC])->all();		
		Yii::$app->session->set('id_client',$id_client);
		
		$ret=['client' => $client, 'zabor'=>$zabor,'zabor_null'=>$zabor_null, 'passtrue'=>1];	
	
	
		} else {	 //пользователь неавторизован
			$ret=['passtrue'=>0];	
		}
		return $this->render('calc',$ret);
	}

	
	
	
	
	
	
	
	
	/*
	* ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ *
    * ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ *
	* ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ *
    * ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ *
	* ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ ** ПРОФЛИСТ *
	*/
	
	
    public function actionCalcproflist() { // расчет профлиста


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {
			
			$model = new CalcProflist(['scenario' => CalcProflist::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);
			
		} else { // первый вывод формы						
			$model = new CalcProflist(['scenario' => CalcProflist::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			$model->calcInit1();
			$smeta=new Smeta();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		
		return $this->render('calcproflist',$ret);

    }
	
	
	
	
	
	
	
	
	
	/* 
	
	* КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА *
	* КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА *
	* КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА *
	* КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА *
	* КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА ** КАЛИТКА *

	*/
	
	

	 public function actionCalckalitka() { // расчет калитки


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {
			
		
			$model = new CalcKalitka(['scenario' => CalcKalitka::STEP1]);
			$smeta=new Smeta();
			
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);					

		} else { // первый вывод формы			
	
			$model = new CalcKalitka(['scenario' => CalcKalitka::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			//читаем смету и для калитки выставляем базовые значения покрытие, грунт, покраска, заглубление
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calckalitka',$ret);

    }
	
	

	
	
	
	
	
	
	
	
	
	
	/* 
	
		* ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** 
		* ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** 
		* ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** 
		* ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** 
		* ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** ВОРОТА *** 
		
	*/
	
	

	 public function actionCalcvorota() { // расчет ворот


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {
			
		
			$model = new CalcVorota(['scenario' => CalcVorota::STEP1]);
			$smeta=new Smeta();
			
			$model->load(Yii::$app->request->post());

			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);						

		} else { // первый вывод формы			
	
			$model = new CalcVorota(['scenario' => CalcVorota::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calcvorota',$ret);

    }
	
	
	
	
	
	
	
	
	
	
	
		/* 
	
		* ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ ***
		* ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ ***
		* ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ ***
		* ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ **** ВОРОТА ОТКАТНЫЕ ***

		
	*/
	
	
	
	
	 public function actionCalcotkatnievorota() { // расчет ворот


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {
			
		
			$model = new CalcOtkatnieVorota(['scenario' => CalcOtkatnieVorota::STEP1]);
			$smeta=new Smeta();
			
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
						
		} else { // первый вывод формы			
	
			$model = new CalcOtkatnieVorota(['scenario' => CalcOtkatnieVorota::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();

		    $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calcotkatnievorota',$ret);

    }
	
	
	
	

	
	/* 
	
			* ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ ***
			* ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ ***
			* ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ ***
			* ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ ***
			* ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ ***
			* ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ **** ФУНДАМЕНТ ***
			
			
	*/
	
	

	 public function actionCalcfundament() { // расчет фундамента


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {		
			
			$model = new CalcFundament(['scenario' => CalcFundament::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
			
		} else { // первый вывод формы			
	
			$model = new CalcFundament(['scenario' => CalcFundament::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calcfundament',$ret);

    }
	
	
	
/* 
	
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				
			
	*/
	
	

	 public function actionCalcparkovka() { // расчет парковки


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {		
			
			$model = new CalcParkovka(['scenario' => CalcParkovka::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
			
		} else { // первый вывод формы			
	
			$model = new CalcParkovka(['scenario' => CalcParkovka::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calcparkovka',$ret);

    }
		
	
/* 
	
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				* ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА**** ПАРКОВКА***
				
			
	*/
	
	

	 public function actionCalcsvai() { // расчет парковки


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {		
			
			$model = new CalcSvai(['scenario' => CalcSvai::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
			
		} else { // первый вывод формы			
	
			$model = new CalcParkovka(['scenario' => CalcSvai::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calcsvai',$ret);

    }
		


/* 
	
			* КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** 
			* КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** 			
			* КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** 			
			* КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** 			
			* КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** 			
			* КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** * КАНАВА *** 			
			
			
	*/
	
	

	 public function actionCalckanava() { // расчет парковки


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {		
			
			$model = new CalcKanava(['scenario' => CalcKanava::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
			
		} else { // первый вывод формы			
	
			$model = new CalcKanava(['scenario' => CalcKanava::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calckanava',$ret);

    }
		

/* 
	
			* ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ ***
			* ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ ***
			* ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ ***
			* ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ *** * ТРАНСПОРТ ***
		
*/


	 public function actionCalctransport() { // расчет парковки


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {		
			
			$model = new CalcTransport(['scenario' => CalcTransport::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
			
		} else { // первый вывод формы			
	
			$model = new CalcTransport(['scenario' => CalcTrasport::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calctransport',$ret);

    }

	
	
/* 
	* ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** 
	* ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** 	
	* ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** 
	* ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** 	
	* ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** 
	* ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** * ШТАКЕТ *** 	
			
	*/
	
	

	 public function actionCalcshtaket() { // расчет парковки


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {		
			
			$model = new CalcShtaket(['scenario' => CalcShtaket::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
			
		} else { // первый вывод формы			
	
			$model = new CalcShtaket(['scenario' => CalcShtaket::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calcshtaket',$ret);

    }			
		
	
	
/* 
       **** РАБИЦА ***	 **** РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ***
       **** РАБИЦА ***	 **** РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ***
       **** РАБИЦА ***	 **** РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ***
       **** РАБИЦА ***	 **** РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ***
       **** РАБИЦА ***	 **** РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ***
       **** РАБИЦА ***	 **** РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ******* РАБИЦА ***

	   
	*/
	
	

	 public function actionCalcrabica() { // расчет парковки


	
		if (!Yii::$app->user->isGuest) {
			
		$c=Yii::$app->session->get('id_client');
		$s =Yii::$app->session->get('id_smeta');

		if (Yii::$app->request->isPjax) {		
			
			$model = new CalcRabica(['scenario' => CalcRabica::STEP1]);
			$smeta=new Smeta();
			$model->load(Yii::$app->request->post());
			$model->calc($c,$s,	$smeta);
			FuncZabor::minusovanieMaterialov ($c);	
			
		} else { // первый вывод формы			
	
			$model = new CalcRabica(['scenario' => CalcRabica::STEP1]);		
			$model->calc_step=1;
			$model->height=Yii::$app->request->get('height');
			$model->len=Yii::$app->request->get('len');
			
			$smeta=new Smeta();
			
			
		
		  $model->calcInit1();
		}
		
		$ret=['model' => $model,'smeta'=>$smeta,'id_client' => $c, 'id_smeta' => $s, 'passtrue'=>1];
		
		} else { // если не залогинены
			$ret=['passtrue'=>0];
		}
		return $this->render('calcrabica',$ret);

    }			
		

		
	
	
    public function actionSetelements()
    {
		
		if (!Yii::$app->user->isGuest) { // если залогинины
		
		$model=new Setelements();
	
		$data=$_POST['Setelements']['inputdata'];
		
		if ($data) {
			
		//проходим все значения и проверям их валидность
		$valid=true;
		$message='';
		foreach ($data as $dt) {
			$model->price=$dt['price'];
			$valid=$model->validate() && $valid;

	 	}
		$message='Внимание! В одном из полей ошибка!';

		if ($valid) {// все данные корректны

			foreach ($data as $dt) {
			Yii::$app->db->createCommand("UPDATE element_type SET price='".$dt['price']."' WHERE id='".$dt['Id']."'") ->execute();
	 	}
		$message='Данные сохранены';
		
		} else {
			// данные не корректны: $errors - массив содержащий сообщения об ошибках
			 $errors = $model->errors;
		}
		
		}

		$model->readAll();
		
		$elementName = new Element_Name();
		$elementNames = $elementName->readAll();
		
		$elementType=new Element_Type();
		$elementTypes = $elementType->readAll();
		
		$ret=['user'=>$user_id,'passtrue'=>'1','elementnames'=>$elementNames,'elementtypes'=>$elementTypes,'model'=>$model,'message'=>$message];	
		} else {

		$ret=['user'=>$user_id,'passtrue'=>'0'];	
		
		}
		
		return $this->render('setelements',$ret);
    }
	
	

	
	
    public function actionMergetable()  {
	  if (!Yii::$app->user->isGuest) {	
		//exit;
		Yii::$app->db->createCommand()->truncateTable('element_type') ->execute();
		$arrs=['Karkas','Proflist','Kalitka','Vorota','OtkatnieVorota','Fundament','Svai','Parkovka','Kanava','Shtaket','Transport','rabica'];
		foreach ($arrs as $arr) {
			$karkas=new Karkas();
			$classname=(self::NS).$arr;
			$input=$classname::find()->all();
			foreach ($input as $item) {
				$element=new Element_Type();
				$element->type=$item->type;
				$element->sub_type=$item->sub_type;
				$element->name=$item->name;
				$element->type_kol=$item->type_kol;
				$element->kol=$item->kol;
				$element->attr=$item->attr;
				$element->price=$item->price;
				$element->ord=$item->ord;
				$element->insert(true);
				$element=NULL;
			}
		}


		
		Yii::$app->db->createCommand()->truncateTable('rabota_type') ->execute();
		foreach ($arrs as $arr) {
			if ($arr=== 'transport') continue;
			$karkas=new Karkas();
			$classname=(self::NS).$arr.'_Rabota';
			$input=$classname::find()->all();
			foreach ($input as $item) {
				$element=new Rabota_Type();
				$element->type=$item->type;
				$element->name=$item->name;
				$element->type_kol=$item->type_kol;
				$element->kol=$item->kol;
				$element->attr=$item->attr;
				$element->price=$item->price;
				$element->insert(true);
				$element=NULL;
			}
		}	
		} //if
	}
	
	
	
	
	
	public function actionGeneratesmeta() {  											
	  if (!Yii::$app->user->isGuest) {	
		if (Yii::$app->request->get('id_client')) {
			 $id_client=Yii::$app->request->get('id_client');
		} else {
		 $id_client= Yii::$app->session->get('id_client');
		}	;	
			//считываем параметры скидки из Settings
			$percent_element=Settings::find()->where(['attr' =>'percent_material_1'])->asArray()->one()['value']; //изменение стоимости материалов
			$percent_work=Settings::find()->where(['attr' =>'percent_work_1'])->asArray()->one()['value'];			//изменение стоимости работ

			$sm=new GenerateSmeta();
			$sm->saveSmetaTxt($id_client,$percent_element,$percent_work);
			
			$client = Client::find()->where(['id_client' => $id_client])->one();
			$client->init();			
			$gs_head=new EditSmeta();
			$gs_head->usermail=$client->email;
			$gs_head->validate();
			$gs_head->document="readsmetatxt";
			
			$smeta_itog=$gs_head->summaSmetaTxt($id_client); 
			$client->summa=$smeta_itog;
			//$client->avans=$smeta_itog*0.5;
			$client->update();
			$gs=SmetaTxt::find()->where(['id_client' => $id_client])->orderBy('poz')->all();
			
			$ret=['client'=>$client, 'gs_head'=>$gs_head, 'gs'=>$gs,'id_client'=>$id_client,'smeta_itog'=>$smeta_itog, 
			'percent_element'=>$percent_element,'percent_work'=>$percent_work];	
			return $this->render('readsmetatxt',$ret);
			
	  }
	}	

	
	
	
	public function actionDogovor() {  											
		
			if (Yii::$app->request->get('id_client')) {
				 $id_client=Yii::$app->request->get('id_client');
			} else {
			 $id_client= Yii::$app->session->get('id_client');
			}	;	

			$client = Client::find()->where(['id_client' => $id_client])->one();
			
			//формируем макет договора
			$dogovor=new Dogovor();
			$dogovor->init($client);
			$dogovor->getBodyAndMake($id_client);
		
			$gs_head=new EditSmeta();
			$gs_head->usermail=$dogovor->email;
			$gs_head->validate();
			$gs_head->document="dogovor";
			

			$ret=['dogovor'=>$dogovor, 'client'=>$client, 'gs_head'=>$gs_head];	
			return $this->render('dogovor',$ret);

		  
	}	

	public function actionDogovortxt() {  	
		return $this->render('dogovor_txt');
	}
	
	
	public function actionAktbegin() {  											

	
			if (Yii::$app->request->get('id_client')) {
				 $id_client=Yii::$app->request->get('id_client');
			} else {
			 $id_client= Yii::$app->session->get('id_client');
			}	;	

			$client = Client::find()->where(['id_client' => $id_client])->one();
			
			//формируем макет 
			$aktbegin=new AktBegin();
			$aktbegin->init($client);
			$aktbegin->getBodyAndMake($id_client);
		
			$gs_head=new EditSmeta();
			$gs_head->usermail=$aktbegin->email;
			$gs_head->validate();
			$gs_head->document="aktbegin";

			$ret=['aktbegin'=>$aktbegin, 'client'=>$client,'gs_head'=>$gs_head];	
			return $this->render('aktbegin',$ret);
	}

	public function actionAktbegintxt() {  	
		return $this->render('aktbegin_txt');
	}
	
	public function actionAktend() {  											
			if (Yii::$app->request->get('id_client')) {
				 $id_client=Yii::$app->request->get('id_client');
			} else {
			 $id_client= Yii::$app->session->get('id_client');
			}	;	

			$client = Client::find()->where(['id_client' => $id_client])->one();
			
			//формируем макет 
			$aktend=new AktEnd();
			$aktend->init($client);
			$aktend->getBodyAndMake($id_client);
		
			$gs_head=new EditSmeta();
			$gs_head->usermail=$aktend->email;
			$gs_head->validate();
			$gs_head->document="aktend";

			$ret=['aktend'=>$aktend, 'client'=>$client,'gs_head'=>$gs_head];	
			return $this->render('aktend',$ret);
		  
	}		
	
	
	public function actionAktendtxt() {  	
		return $this->render('aktend_txt');
	}
	
	
		public function actionEditsmeta () {
		
		if (!Yii::$app->user->isGuest) {	

			if (Yii::$app->request->get('id_client')) {
				 $id_client=Yii::$app->request->get('id_client');
			} else {
			 $id_client= Yii::$app->session->get('id_client');
			}	;	
			
			if ($items=Yii::$app->request->post()) {
				
				//var_dump($items);
				
				$k_element=$items['EditSmeta']["add_element"];
				$k_work=$items['EditSmeta']["add_work"];
	
				
				if ($k_element=="") $k_element=0;
				if ($k_work=="") $k_work=0;


				foreach ($items["col"] as $item) {
					$sm=SmetaTxt::find()->where(['id_client' => $id_client, 'poz'=>$item[1]])->one();
					$sm->name=$item[2];
					$sm->kol=$item[3];
					$sm->ed=$item[4];
					$sm->price=($item[6]=='e') ? $item[5]+$item[5]*$k_element/100 : $item[5]+$item[5]*$k_work/100;
					$sm->summa=$item[3]*$sm->price;					
					$sm->update();			
				}
				

			}

			$client = Client::find()->where(['id_client' => $id_client])->one();
			$client->init();
						
			$gs_head=new EditSmeta();
			$gs_head->usermail=$client->email;
			$gs_head->validate();
			$gs_head->document="editsmeta";
			$gs_head->add_element='';
			$gs_head->add_work='';
			
			$smeta_itog=$gs_head->summaSmetaTxt($id_client); 			
			
			$gs=SmetaTxt::find()->where(['id_client' => $id_client])->orderBy('poz')->all();
			
			$ret=['client'=>$client, 'gs_head'=>$gs_head, 'gs'=>$gs,'id_client'=>$id_client,'smeta_itog'=>$smeta_itog];	
			return $this->render('editsmeta',$ret);
	}
	
	
	
 }	
	
	
	
	

		public function actionReadsmetatxt () {
		
		if (!Yii::$app->user->isGuest) {	

			if (Yii::$app->request->get('id_client')) {
				 $id_client=Yii::$app->request->get('id_client');
			} else {
			 $id_client= Yii::$app->session->get('id_client');
			}	;	
			

			$client = Client::find()->where(['id_client' => $id_client])->one();
			$client->init();
					
			$gs_head=new EditSmeta();
			$gs_head->usermail=$client->email;
			$gs_head->validate();
			$gs_head->document="readsmetatxt";
			$gs_head->add_element='';
			$gs_head->add_work='';
			
			$smeta_itog=$gs_head->summaSmetaTxt($id_client); 		
			if ($smeta_itog==0) {
				GenerateSmeta::saveSmetaTxt($id_client);			//генерируем смету			
				
			}
			
			$gs=SmetaTxt::find()->where(['id_client' => $id_client])->orderBy('poz')->all();
			
			$ret=['client'=>$client, 'gs_head'=>$gs_head, 'gs'=>$gs,'id_client'=>$id_client,'smeta_itog'=>$smeta_itog];	
			return $this->render('readsmetatxt',$ret);
	}
	
	
	
 }	
 
	
	
	public function actionSendsmeta() {  													//на странице calc нажата кнопка СМЕТА КЛИЕНТА
		
		if (Yii::$app->request->get('id_client')) {
			 $id_client=Yii::$app->request->get('id_client');
		} else {
			$id_client= Yii::$app->session->get('id_client');
		}
		$client = Client::find()->where(['id_client' => $id_client])->one();
		
		$gs_head=new EditSmeta();
		$smeta_itog=$gs_head->summaSmetaTxt($id_client); 	
		
		$gs=SmetaTxt::find()->where(['id_client' => $id_client])->orderBy('poz')->all();
	
		$ret=['client'=>$client, 'gs_head'=>$gs_head, 'gs'=>$gs,'id_client'=>$id_client,'smeta_itog'=>$smeta_itog];	

		return $this->render('sendsmeta',$ret);
	
	}
		
	
		public function actionClientlist () {
		
		if (!Yii::$app->user->isGuest) {	

			if (Yii::$app->request->get('id_client')) {
				 $id_client=Yii::$app->request->get('id_client');
			} else {
			 $id_client= Yii::$app->session->get('id_client');
			}	;	
			
			$searchModel = new Client();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$ret=['clients'=>$clients, 'dataProvider' => $dataProvider, 'searchModel' => $searchModel];	
			return $this->render('clientlist',$ret);
	}

	
 }	
	
	
	public function actionClientdelete() {
		
		if (!Yii::$app->user->isGuest) {	

			if (Yii::$app->request->get('id_client')) {
				 $id_client=Yii::$app->request->get('id_client');
			} else {
			 $id_client= Yii::$app->session->get('id_client');
			}	;	
			
			\Yii::$app->db->createCommand()->delete('zabor', ['id_client'=>$id_client]) ->execute();
			\Yii::$app->db->createCommand()->delete('smeta', ['user_id'=>$id_client]) ->execute();
			\Yii::$app->db->createCommand()->delete('input_form', ['user_id'=>$id_client]) ->execute();
			\Yii::$app->db->createCommand()->delete('smetatxt', ['id_client'=>$id_client]) ->execute();			
			\Yii::$app->db->createCommand()->delete('clients', ['id_client'=>$id_client]) ->execute();			
			
			$searchModel = new Client();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$ret=['clients'=>$clients, 'dataProvider' => $dataProvider, 'searchModel' => $searchModel];	
			return $this->render('clientlist',$ret);
			
		}//if guest


	}
	
	
	//Yii::$app->db->createCommand("UPDATE rabota_type SET price='".$dt['price']."' WHERE id='".$dt['Id']."'") ->execute();
	
	 public function actionListworknames()   {
		
		if (!Yii::$app->user->isGuest) { 															// если залогинины
		
			$dataProviders=Rabota_Name::find()->orderBy(['Id'=>ASC])->asArray()->all();		

			$ret=['dataproviders' => $dataProviders];	
			return $this->render('listworknames',$ret);
			
		}
    }
	

	 public function actionListelementnames()   {
		
		if (!Yii::$app->user->isGuest) { 															// если залогинины
		
			$dataProviders=Element_Name::find()->orderBy(['Id'=>ASC])->asArray()->all();		

			$ret=['dataproviders' => $dataProviders];	
			return $this->render('listelementnames',$ret);
			
		}
    }	
	
	 public function actionListworktypes()   {
		
		if (!Yii::$app->user->isGuest) { 															// если залогинины
			
			$type_id=htmlspecialchars(\Yii::$app->request->get('type_id'));	
			
			if (is_numeric ($type_id)) {
				
				$searchModel=new Rabota_Type();
			
				$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
				
				if (is_numeric($type=\Yii::$app->request->get('type_id'))) {				
				 $catname=Rabota_Name::find()->select(['name'])->where(['type_id'=>$type])->asArray()->one();		
				}		
				
				$ret=[ 'dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'catname'=>$catname['name'] ];				
				return $this->render('listworktypes',$ret);
			}
		}
    }	
	

	 public function actionListelementtypes()   {
		
		if (!Yii::$app->user->isGuest) { 															// если залогинины
			
			$type_id=htmlspecialchars(\Yii::$app->request->get('type_id'));	
			
			if (is_numeric ($type_id)) {
				
				$searchModel=new Element_Type();
			
				$dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
				
				if (is_numeric($type=\Yii::$app->request->get('type_id'))) {				
				 $catname=Element_Name::find()->select(['name'])->where(['type_id'=>$type])->asArray()->one();		
				}		
				
				$ret=[ 'dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'catname'=>$catname['name'] ];				
				return $this->render('listelementtypes',$ret);
			}
		}
    }	
	
	
	
	 public function actionWorklistedit()   {
		 
		if (!Yii::$app->user->isGuest) { 
		
		  if (is_numeric($id=htmlspecialchars(\Yii::$app->request->get('Id')))) {				
			  $line=Rabota_Type::find()->where(['Id'=>$id])->one();		
			 
			  $ret=['line'=>$line];
			  return $this->render('worklistedit',$ret);
		  }
		  
		}
		
	 }


	 public function actionWorklisteditsave()   {
		 
		if (!Yii::$app->user->isGuest) { 
		
			$id=htmlspecialchars(Yii::$app->request->post()['Rabota_Type']['Id']);
	
			$workline=Rabota_Type::find()->where(['Id' =>$id])->one();	
				
			if (($workline->load(\Yii::$app->request->post())) && ($workline->validate())) {
				$workline->update(false);
			}
			  $ret=['line'=>$workline];
			  return $this->render('worklistedit',$ret);
		  }
		  
	
	}
		
	 
	 public function actionElementlistedit()   {
		 
		if (!Yii::$app->user->isGuest) { 
		
		  if (is_numeric($id=htmlspecialchars(\Yii::$app->request->get('Id')))) {				
			  $line=Element_Type::find()->where(['Id'=>$id])->one();		
			 
			  $ret=['line'=>$line];
			  return $this->render('elementlistedit',$ret);
		  }
		  
		}
		
	 }


	 public function actionElementlisteditsave()   {
		 
		if (!Yii::$app->user->isGuest) { 
		
			$id=htmlspecialchars(Yii::$app->request->post()['Element_Type']['Id']);
	
			$workline=Element_Type::find()->where(['Id' =>$id])->one();	
				
			if (($workline->load(\Yii::$app->request->post())) && ($workline->validate())) {
				$workline->update(false);
			}
			  $ret=['line'=>$workline];
			  return $this->render('elementlistedit',$ret);
		  }
		  
	
	}	 
	 
	 
	
}