<?php

require_once("course.SS.class.php");
require_once("student.SS.class.php");
//require_once("course.SS.class.php");


class SS_search{

	function __construct($upN){
		if(!$this->isValidUPNumber($upN)){
			throw new exception("Invalid UP number passed!");
		}

		$res=$this->makeCurlPostReq("https://sigarra.up.pt/up/pt/u_fest_geral.querylist", "pv_curso_id=&pv_ramo_id=&pa_inst=18380&pa_inst=18395&pa_inst=18379&pa_inst=18493&pa_inst=18383&pa_inst=18487&pa_inst=18491&pa_inst=18490&pa_inst=18381&pa_inst=18492&pa_inst=18494&pa_inst=18384&pa_inst=18489&pa_inst=18382&PV_NUMERO_DE_ESTUDANTE=".substr($upN, 2)."&PV_NOME=&PV_EMAIL=&PV_TIPO_DE_CURSO=&pv_curso_nome=&pv_ramo_nome=&PV_AREA_FORM_CONT_ID=&PV_ESTADO=&PV_EM=&PV_ATE=&PV_1_INSCRICAO_EM=&PV_ATE_2=&PV_TIPO=&pv_n_registos=20");
		var_dump($this->interpretResTable($res[1]));
	}


	function interpretResTable($res){
		libxml_use_internal_errors( true);
		$doc = new DOMDocument;
		$doc->loadHTML( $res);
		$xpath = new DOMXpath( $doc);
		for($i=1;$i<$xpath->query('//table[@class="dados"]')->item(0)->childNodes->length;$i++){

			foreach($xpath->query('//table[@class="dados"]')->item(0)->childNodes->item($i)->childNodes as $node){
				if(!isset($node->tagName)){
					continue;
				}
				var_dump($node->textContent);
			}
		}
		
		return "a";
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