<?php

class SS_course{
    public $name;
    public $sigla;
    public $code=null;

    function __construct($name){
		
        $this->name	= $name;
	}
	
	/*  getCode may be a heavy function. Use with care!
	* 	CURL required.
	*/
	
	public function getCode(){
		if($this->code!=NULL){
			return $this->code;
		}
		$this->res=$this->makeCurlPostReq("https://sigarra.up.pt/up/pt/u_cursos_geral.querylist", "pa_inst=18380&pa_inst=18395&pa_inst=18379&pa_inst=18493&pa_inst=18383&pa_inst=18487&pa_inst=18491&pa_inst=18490&pa_inst=18381&pa_inst=18492&pa_inst=18494&pa_inst=18384&pa_inst=18489&pa_inst=18382&pv_tipo=&pv_nome=".urlencode(utf8_decode($this->name))."&pv_sigla=&PV_ACTIVO_NO_ANO_LECTIVO=")[1];
		$this->res=utf8_encode($this->res);
		libxml_use_internal_errors(true);
		$doc = new DOMDocument;
		$doc->loadHTML( utf8_decode($this->res));
		$xpath = new DOMXpath( $doc);
		if($xpath->query('//div[@id="conteudoinner"]//td[2]/a')->length==0){
			$this->code="";
			$this->sigla="";
			return "";
		}else{
			$url=$xpath->query('//div[@id="conteudoinner"]//td[2]/a')->item(0)->getAttribute("href");
			$exploded=explode("=", $url);
			$this->code = end($exploded);
			$this->sigla = $xpath->query('//div[@id="conteudoinner"]//td[3]')->item(0)->textContent;
			return end($exploded);
		}
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

	function __toString(){
		$this->getCode();
		return sprintf("%05d - ", $this->code) . "[".$this->sigla."] $this->name".PHP_EOL;
	}

}

?>
