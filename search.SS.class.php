<?php

require_once("course.SS.class.php");
require_once("student.SS.class.php");
//require_once("course.SS.class.php");


class SS_search{

	private $res; // to keep the curl response
	private $number;
	public $student;
	function __construct($upN, $typeOfSearch='name'){

		if(!$this->isValidUPNumber($upN)){
			throw new exception("Invalid UP number passed!");
		}

		$this->number=$upN;
		


		/*	A normal search will only retrieve a name. A course search must be indicated, if required.	*/

		switch($typeOfSearch){
			case 'name':
				//if($verbose) echo "Proceding with a name search...".PHP_EOL;
				$this->res=file_get_contents("http://sigarra.up.pt/icbas/pt/fest_geral.cursos_list?pv_num_unico=".substr($upN, 2));
				$this->res=utf8_encode($this->res);
				$this->interpretNameResult();
			break;
			case 'course':
				//if($verbose) echo "Proceding with a course search...".PHP_EOL;
				$this->res=$this->makeCurlPostReq("https://sigarra.up.pt/up/pt/u_fest_geral.querylist", "pv_curso_id=&pv_ramo_id=&pa_inst=18380&pa_inst=18395&pa_inst=18379&pa_inst=18493&pa_inst=18383&pa_inst=18487&pa_inst=18491&pa_inst=18490&pa_inst=18381&pa_inst=18492&pa_inst=18494&pa_inst=18384&pa_inst=18489&pa_inst=18382&PV_NUMERO_DE_ESTUDANTE=".substr($upN, 2)."&PV_NOME=&PV_EMAIL=&PV_TIPO_DE_CURSO=&pv_curso_nome=&pv_ramo_nome=&PV_AREA_FORM_CONT_ID=&PV_ESTADO=&PV_EM=&PV_ATE=&PV_1_INSCRICAO_EM=&PV_ATE_2=&PV_TIPO=&pv_n_registos=20")[1];
				$this->res=utf8_encode($this->res);
				$this->interpretCoursesTable();
			break;
			default:
				throw new exception("The requested type of search is unknown.", -10);
			break;
		}

	}

	public function interpretNameResult(){
		if(strpos($this->res, "Estudante não encontrado.")!=FALSE){
			throw new exception("The specified ID does not exist!".PHP_EOL, -1);
		}
		libxml_use_internal_errors(true);
		$doc = new DOMDocument;
		$doc->loadHTML( utf8_decode($this->res));
		$xpath = new DOMXpath( $doc);
		var_dump($xpath->query('//div[@id="conteudoinner"]/h1')->item(1)->textContent);
		$name=$xpath->query('//div[@id="conteudoinner"]/h1')->item(0)->textContext;
		$this->student = new SS_student($this->number, $name);
	}

	
	public function countResults():int{
		libxml_use_internal_errors(true);
		$doc = new DOMDocument;
		$doc->loadHTML( utf8_decode($this->res));
		$xpath = new DOMXpath( $doc);
		if($xpath->query('//div[@id="erro"]')->length!=0){
			return 0;
		}
		return $xpath->query('//table[@class="dados"]')->item(0)->childNodes->length-1;
	}

	function interpretCoursesTable(){
		if(strpos($this->res, "Não foi encontrado qualquer registo de Estudantes.") !=FALSE){
			throw new exception("The specified ID does not exist!".PHP_EOL, -1);
		}

		$names=array();
		$course=array();
		libxml_use_internal_errors(true);
		$doc = new DOMDocument;
		$doc->loadHTML( utf8_decode($this->res));
		$xpath = new DOMXpath( $doc);
		for($i=1;$i<$xpath->query('//table[@class="dados"]')->item(0)->childNodes->length;$i++){
			
			array_push($course, new SS_Course($xpath->query('//table[@class="dados"]')->item(0)->childNodes->item($i)->childNodes->item(4)->textContent));
			
			array_push($names, $xpath->query('//table[@class="dados"]')->item(0)->childNodes->item($i)->childNodes->item(2)->textContent);
			
			foreach($xpath->query('//table[@class="dados"]')->item(0)->childNodes->item($i)->childNodes as $node){
				if(!isset($node->tagName)){
					continue;
				}
				var_dump($node->textContent);
			}
		}
		$name=array_unique($names);
		if(count($name) !== 1){
			var_dump($names);
			
			throw new exception("The impossible happened! We got two different names for the same number!".PHP_EOL);
		}
		
		$this->student = new SS_student($this->number, $name[0]);
		$this->courses = $course;
				
		//echo $name;

		return NULL;
	}

	function makeCurlPostReq($url, $postStr){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
		curl_setopt($ch, CURLOPT_HEADER, true); //if you want headers

		$output=curl_exec($ch);

		curl_close($ch);

		return explode("\r\n\r\n", $output, 2);
	}

	function isValidUPNumber($upN):bool{
		if($upN[0]!='u' || $upN[1]!='p')
			return false;

		if(strlen($upN)!=11)
			return false;

		$upN=substr($upN, 2);
		if(!is_numeric($upN))
			return false;

		// So far, so good.
		return true;
	}



}

?>
