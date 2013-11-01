<?php

/*
Copyright 2013 de SEIJI MANOAN SEO
Este arquivo é parte do programa POVINDICA BRASIL. O POVINDICA BRASIL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da [GNU General Public License OU GNU Affero General Public License] como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença. Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a licença para maiores detalhes. Você deve ter recebido uma cópia da [GNU General Public License OU GNU Affero General Public License], sob o título "LICENCA.txt", junto com este programa, se não, acesse http://www.gnu.org/licenses/
*/

namespace
{	session_start ();

	if (file_exists ("./model.php") == false) die (header ("HTTP/1.1 404 Not Found"));
	else header ("Content-type: application/json; charset=utf-8");
	
	date_default_timezone_set ("ETC/GMT-3");
	define ("DEMETER", "federalst");
	
	$method = count ($_GET) > count ($_POST) ? "get" : "post";
	$factor = 0;
	
	if (include ("./model.php"))
	{
		class Functions extends Replier
		{	public function __construct ($i_modular = 0, $i_method = "post", $i_access = null)
			{	parent::__construct ($i_modular, $i_method, $i_access);
			}
			public function j_getRuledMenu ()
			{	$dear = "SELECT t.name, t.modular, t.type, t.ordering, p.banned FROM demperms AS p INNER JOIN (SELECT name, modular, type, ordering, job, uid FROM demtools WHERE job = 'enabled') AS t ON p.what = t.uid WHERE (p.whom = '{$_SESSION["dados"]["login"]}' OR p.whom = '{$_SESSION["dados"]["nivel_acesso"]}') AND t.modular NOT IN (SELECT modular FROM demperms JOIN (SELECT uid, modular FROM demtools) AS m ON demperms.what = m.uid WHERE (whom = '{$_SESSION["dados"]["login"]}' AND banned = 1) OR (whom = '{$_SESSION["dados"]["nivel_acesso"]}' AND banned = 1)) GROUP BY t.modular ORDER BY t.ordering ASC;";
				return $relational = $this->__matrix ($this->database->query ($dear));
			}
			public function j_getNameByCPF ()
			{	try
				{	$setting[0]["codigoCPF"] = isset ($this->method["person"]) && strlen ($this->method["person"]) == 11 ? $this->method["person"] : null;

					$byCPF = new Hermes\sourceContas;
					
					$setting[0]["dadosCPF"] = $byCPF->doJutsu ($setting[0]["codigoCPF"]);
					
					$_SESSION[__CLASS__]["pessoaCPF"] = $relational["pessoaCPF"] = $setting[0]["dadosCPF"]["data"]["infoName"] && strlen ($setting[0]["dadosCPF"]["data"]["infoName"]) ? $setting[0]["dadosCPF"]["data"]["infoName"] : null ;
					$_SESSION[__CLASS__]["codigoCPF"] = $relational["codigoCPF"] = $setting[0]["dadosCPF"]["data"]["infoName"] && strlen ($setting[0]["dadosCPF"]["data"]["infoName"]) ? $setting[0]["codigoCPF"] : null;
					
					if (array_search (null, $setting[0]))
					foreach ($setting[0] as $name => $value)
					if ($value == null && isset ($_SESSION[__CLASS__][$name]))
					unset ($_SESSION[__CLASS__][$name]);
					
					// print_r ($setting[0]);
					// print_r ($_SESSION);
					
					if ($this->database->error) throw new Exception ($this->database->error, $this->database->errno);
					else if (array_search (null, $setting[0])) throw new Exception ("Termo de pesquisa inválido", 404);
					else if ((is_array ($relational) && array_search (null, $relational)) || $relational == null) throw new Exception ("Sem resultados para pesquisa", 200);
					else return array ("response" => "success", "status" => 2000, "desc" => "", "callback" => $relational);
				}
				catch (Exception $e)
				{	if (isset ($_SESSION[__CLASS__])) unset ($_SESSION[__CLASS__]);
					return array ("response" => "error", "status" => $e->getCode (), "desc" => $e->getMessage (), "callback" => []);
				}
			}
			public function j_getImageOfCaptcha ()
			{	try
				{	$byEleitor = new Hermes\sourceEleitoral (true);
					$relational = Hermes\sourceEleitoral::__getOutput ();
					
					if ($this->database->error) throw new Exception ($this->database->error, $this->database->errno);
					else if (array_search (null, $relational)) throw new Exception ("Sem resultados para pesquisa", 200);
					else return array ("response" => "success", "status" => 2000, "desc" => "", "callback" => ["imageCaptcha" => $relational["imageCaptcha"]]);
				}
				catch (Exception $e)
				{	if (isset ($_SESSION[__CLASS__])) unset ($_SESSION[__CLASS__]);
					return array ("response" => "error", "status" => $e->getCode (), "desc" => $e->getMessage (), "callback" => []);
				}
			}
			public function j_getTrueCitizen ()
			{	try
				{	$setting[0]["born"] = $_SESSION[__CLASS__]["born"] = isset ($this->method["born"]) && preg_match ("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $this->method["born"]) ? $this->method["born"] : null;
					$setting[0]["mommy"] = $_SESSION[__CLASS__]["mommy"] = isset ($this->method["mommy"]) && strlen ($this->method["mommy"]) > 0 ? $this->method["mommy"] : null;
					$setting[0]["captcha"] = isset ($this->method["captcha"]) && strlen ($this->method["captcha"]) > 0 ? $this->method["captcha"] : null;
				
					$byEleitor = new Hermes\sourceEleitoral (false);
					
					$setting[1] = Hermes\sourceEleitoral::__getOutput ();
					$setting[2] = Hermes\sourceContas::__getOutput ();

					$setting[0]["token"] = isset ($setting[1]["htmlToken"]) && strlen ($setting[1]["htmlToken"]) > 0 ? $setting[1]["htmlToken"] : null;
					$setting[0]["jsessionid"] = isset ($setting[1]["setSession"]["JSESSIONID"]) && strlen ($setting[1]["setSession"]["JSESSIONID"]) > 0 ? $setting[1]["setSession"]["JSESSIONID"] : null;
					$setting[0]["person"] = isset ($setting[2]["infoName"]) && strlen ($setting[2]["infoName"]) > 0 && empty ($this->method["person"]) ? $setting[2]["infoName"] : ($this->__compareBoth ($setting[2]["infoName"], $this->method["person"]) ? $this->method["person"] : null);
					
					// Do the TSE job
					$setting[3] = $byEleitor->doJutsu ($setting[0]);
					
					function transformPlace ($entry, $parent)
					{	// Detect it is Municipio or not
						if ("6833789c252b44ce3701e5146a9dbeb9" != $parent)
						return (isset ($entry) && strlen ($entry) ? $entry : null);
						
						// Is it Municipio? Filter on it then
						$map = ["localCidade", "localEstado"];
						$entry = preg_split ("/-/m", $entry);
						foreach ($map as $head => $body)
						{	$entry[$body] = trim ($entry[$head], chr(0xC2).chr(0xA0)."\t\r\n ");
							unset ($entry[$head]);
						}
						return $entry;
					}
					
					$cache["headers"] = [];
					$cache["dom"] = new DOMDocument ();
					$cache["dom"]->loadHTML ($setting[3]["body"]);
					$cache["trest"] = $cache["dom"]->getElementsByTagName ("td");
					
					foreach ($cache["trest"] as $el)
					if (substr (trim ($el->nodeValue), -1) == ":")
					{	$last = md5 (utf8_decode (substr (trim ($el->nodeValue), 0, -1)));
						$cache["headers"][$last] = (string) "";
					}
					else if (empty ($last)) continue;
					else
					{	$cache["headers"][$last] = $_SESSION[__CLASS__][$last] = (isset ($last) && strlen ($last)) ? transformPlace (preg_replace ("/[\r?\n|\t]/m", "", trim ($el->nodeValue)), $last) : null;
						unset ($last);
					}
					$relational = isset ($cache["headers"]) && count ($cache["headers"]) ? $cache["headers"] : null;
					
					if (array_search (null, $setting[0]))
					foreach ($setting[0] as $name => $value)
					if ($value == null && isset ($_SESSION[__CLASS__][$name]))
					unset ($_SESSION[__CLASS__][$name]);
					
					// print_r ($setting[0]);
					// print_r ($_SESSION);
					
					if ($this->database->error) throw new Exception ($this->database->error, $this->database->errno);
					else if (array_search (null, $setting[0])) throw new Exception ("Termo de pesquisa inválido", 404);
					else if ((is_array ($relational) && array_search (null, $relational)) || $relational == null) throw new Exception ("Sem resultados para pesquisa", 200);
					else return array ("response" => "success", "status" => 2000, "desc" => "", "callback" => $relational);
				}
				catch (Exception $e)
				{	if (isset ($_SESSION[__CLASS__])) unset ($_SESSION[__CLASS__]);
					return array ("response" => "error", "status" => $e->getCode (), "desc" => $e->getMessage (), "callback" => []);
				}
			}
			public function j_getNewUser ()
			{	try
				{	$setting[0]["login"] = isset ($this->method["username"]) && strlen ($this->method["username"]) > 2 ? $this->method["username"] : null;
					$setting[0]["senha"] = isset ($this->method["password"]) && strlen ($this->method["password"]) > 0 ? md5 ($this->method["password"]) : null;
					$setting[0]["email"] = isset ($this->method["email"]) && strlen ($this->method["email"]) > 5 ? filter_var ($this->method["email"], FILTER_VALIDATE_EMAIL) : null;
					
					$setting[1]["dataNascimento"] = isset ($_SESSION[__CLASS__]["born"]) ? $_SESSION[__CLASS__]["born"] : null;
					$setting[1]["nomeMae"] = isset ($_SESSION[__CLASS__]["mommy"]) ? $_SESSION[__CLASS__]["mommy"] : null;
					
					$setting[1]["tituloEleitor"] = isset ($_SESSION[__CLASS__]["411f990cccf500604e3684b6989ed905"]) ? $_SESSION[__CLASS__]["411f990cccf500604e3684b6989ed905"] : null;
					$setting[1]["nomeEleitor"] = isset ($_SESSION[__CLASS__]["0ad5651caa3610992037f0dde2f502ce"]) ? $_SESSION[__CLASS__]["0ad5651caa3610992037f0dde2f502ce"] : null;
					$setting[1]["zonaEleitor"] = isset ($_SESSION[__CLASS__]["918a51f30ff70dba28eb98bd2268687b"]) ? $_SESSION[__CLASS__]["918a51f30ff70dba28eb98bd2268687b"] : null;
					$setting[1]["secaoEleitor"] = isset ($_SESSION[__CLASS__]["5822de0dac0329842e2a1b5e9f59381d"]) ? $_SESSION[__CLASS__]["5822de0dac0329842e2a1b5e9f59381d"] : null;
					$setting[1]["localEleitor"] = isset ($_SESSION[__CLASS__]["509820290d57f333403f490dde7316f4"]) ? $_SESSION[__CLASS__]["509820290d57f333403f490dde7316f4"] : null;
					$setting[1]["lograEleitor"] = isset ($_SESSION[__CLASS__]["0cb728c570dffe55ca61a9d664e58bc8"]) ? $_SESSION[__CLASS__]["0cb728c570dffe55ca61a9d664e58bc8"] : null;
					$setting[1]["cidadeEleitor"] = isset ($_SESSION[__CLASS__]["6833789c252b44ce3701e5146a9dbeb9"]) && isset ($_SESSION[__CLASS__]["6833789c252b44ce3701e5146a9dbeb9"]["localCidade"]) ? $_SESSION[__CLASS__]["6833789c252b44ce3701e5146a9dbeb9"]["localCidade"] : null;
					$setting[1]["estadoEleitor"] = isset ($_SESSION[__CLASS__]["6833789c252b44ce3701e5146a9dbeb9"]) && isset ($_SESSION[__CLASS__]["6833789c252b44ce3701e5146a9dbeb9"]["localEstado"]) ? $_SESSION[__CLASS__]["6833789c252b44ce3701e5146a9dbeb9"]["localEstado"] : null;
					$setting[1]["nome"] = isset ($_SESSION[__CLASS__]["pessoaCPF"]) ? $_SESSION[__CLASS__]["pessoaCPF"] : null;
					$setting[1]["codigoCPF"] = isset ($_SESSION[__CLASS__]["codigoCPF"]) ? $_SESSION[__CLASS__]["codigoCPF"] : null;
					
					foreach ($setting[1] as $name => &$value)
					if ($value == null && isset ($_SESSION[__CLASS__][$name])) unset ($_SESSION[__CLASS__][$name]);
					else if ($value == "" && isset ($_SESSION[__CLASS__][$name])) $value = null;
					else continue;
					
					$setting[0]["email"] = is_string (filter_var ($this->method["email"], FILTER_VALIDATE_EMAIL)) ? filter_var ($this->method["email"], FILTER_VALIDATE_EMAIL) : null;
					
					$setting[0] = array_merge ($setting[0], $setting[1]);
					
					$dear = $this->__filterSplitIntoTable (array_filter ($setting[0]));
					$relational = $this->database->query ("INSERT IGNORE INTO usuarios ({$dear["cols"]}) VALUES ({$dear["rows"]});");
					
					// print_r ($setting[0]);
					// print_r ($_SESSION[__CLASS__]);
					
					if ($this->database->error) throw new Exception ($this->database->error, $this->database->errno);
					else if (array_search (null, $setting[0])) throw new Exception ("Termo de pesquisa inválido", 404);
					else if ((is_array ($relational) && array_search (null, $relational)) || $relational == null) throw new Exception ("Sem resultados para pesquisa", 200);
					else return array ("response" => "success", "status" => 2000, "desc" => "", "callback" => $relational);
				}
				catch (Exception $e)
				{	// if (isset ($_SESSION[__CLASS__])) unset ($_SESSION[__CLASS__]);
					return array ("response" => "error", "status" => $e->getCode (), "desc" => $e->getMessage (), "callback" => []);
				}
			}
			public function __compareBoth ($former, $later)
			{	if (empty ($former) || empty ($later))
				return false;
				
				$alike = 0;
				similar_text (strtoupper (urldecode ($former)), strtoupper (urldecode ($later)), $alike);
				if (number_format ($alike, 0) > 66) return true;
				else return false;
			}
		}
		$system = new Functions ($factor, $method);
	}
}
namespace Hermes
{
class searchIdentity
{	protected $author = ["name" => "Seiji Manoan Seo", "contact" => "seijimanoan@live.com", "datetime" => "2013-10-23 14:00:00"];
	protected $bugFields = ["xml" => true];
	public function __construct ($xml = true)
	{	try
		{	// header ("Content-type: application/json; charset=utf-8");
			libxml_use_internal_errors ($this->bugFields["xml"] = $xml);
			date_default_timezone_set ("ETC/GMT-3");
			
			$this->__setSession ();
		}
		catch (Exception $e)
		{
		}
	}
	public function __setSession ()
	{	if (session_status () == PHP_SESSION_NONE) session_start ();
		return session_status ();
	}
	public function __getDataURI ($image, $mime = "image/jpeg")
	{	if (isset ($image))
			return 'data:' . (function_exists ("mime_content_type") ? mime_content_type ($image) : $mime) . ';base64,' . base64_encode ($image);
		else
			return false;
	}
}
class sourceContas extends searchIdentity
{	public function __construct ()
	{	parent::__construct ();
		
		$this->doIndexation ();
	}
	public function __destruct ()
	{	try
		{	$output["crc"] = hash_file ("crc32b", __FILE__);
			$output["xml"] = $this->bugFields["xml"] ? libxml_get_errors () : libxml_clear_errors ();
			
			return $output;
		}
		catch (Exception $e)
		{	return array ("response" => "error", "status" => $e->getCode (), "desc" => $e->getMessage (), "callback" => []);
		}
	}
	public static function __getOutput ()
	{	return (isset ($_SESSION[__CLASS__]) ? $_SESSION[__CLASS__] : []);
	}
	protected $bugFields = ["xml" => true];
	protected $mapFields = ["viewState" => "javax.faces.ViewState", "infoName" => "nome"];
	public $sesStorage = [];
	public function doIndexation ()
	{	$loginHeaders = array
		(	"Host: contas.tcu.gov.br"
		);
	
		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, "https://contas.tcu.gov.br/tcu/Web/Siga/GestaoPerfil/CadastrarUsuarioExterno_v4/AcessoSemToken.faces");
		curl_setopt ($ch, CURLOPT_HEADER, true);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_NOBODY, false);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $loginHeaders);
		curl_setopt ($ch, CURLOPT_ENCODING, "gzip, deflate");
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0");
		curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_CAINFO, getcwd()."\general.pem");
		
		$output["body"] = curl_exec ($ch);
		$output["head"] = curl_getinfo ($ch, CURLINFO_HEADER_OUT);
		$output["error"] = curl_error ($ch);
		curl_close ($ch);
		
		$cache["headers"] = array ();
		$cache["dom"] = new \DOMDocument ();
		$cache["dom"]->loadHTML ($output["body"]);
		$cache["trest"] = $cache["dom"]->getElementsByTagName ("input");
		foreach ($cache["trest"] as $el)
			if (in_array ($el->getAttribute ("name"), $this->mapFields))
				$_SESSION[__CLASS__][array_search ($el->getAttribute ("name"), $this->mapFields)] = $cache["headers"][array_search ($el->getAttribute ("name"), $this->mapFields)] = $el->getAttribute ("value");
		if (preg_match ('/Set-Cookie: (.*)\r\n/mi', $output["body"], $cache["jsession"]))
			$_SESSION[__CLASS__]["setCookie"] = $cache["headers"]["jsessionid"] = $cache["jsession"][1];

		$output["data"] = $cache["headers"];
		$output["this"] = __CLASS__;

		unset ($cache);	
		return $output;
	}
	public function doJutsu ($entry)
	{	$loginHeaders = array
		(	"Host: contas.tcu.gov.br"
		);
		$url = "https://contas.tcu.gov.br/tcu/Web/Siga/GestaoPerfil/CadastrarUsuarioExterno_v4/AcessoSemToken.faces?AJAXREQUEST=_viewRoot&cpf=".$entry."&nome=&email=&telefone=&pergunta=-1&resposta=&senha=&confirmarSenha=&form_SUBMIT=1&form%3A_link_hidden_=&form%3A_idcl=&javax.faces.ViewState=".urlencode($_SESSION[__CLASS__]["viewState"])."&form%3A_idJsp6=form%3A_idJsp6&";

		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, false);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_NOBODY, false);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $loginHeaders);
		curl_setopt ($ch, CURLOPT_ENCODING, "gzip, deflate");
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0");
		curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_CAINFO, getcwd()."\general.pem");
		curl_setopt ($ch, CURLOPT_COOKIE, $_SESSION[__CLASS__]["setCookie"]);

		$output["body"] = curl_exec ($ch);
		$output["head"] = curl_getinfo ($ch, CURLINFO_HEADER_OUT);
		$output["error"] = curl_error ($ch);
		curl_close ($ch);
		
		$mapFields = ["viewState" => "javax.faces.ViewState", "htmlToken" => "org.apache.struts.taglib.html.TOKEN"];

		$cache["headers"] = array ();
		$cache["dom"] = new \DOMDocument ();
		$cache["dom"]->loadHTML ($output["body"]);
		$cache["trest"] = $cache["dom"]->getElementsByTagName ("input");
		foreach ($cache["trest"] as $el)
			if (in_array ($el->getAttribute ("name"), $this->mapFields))
				$_SESSION[__CLASS__][array_search ($el->getAttribute ("name"), $this->mapFields)] = $cache["headers"][array_search ($el->getAttribute ("name"), $this->mapFields)] = $el->getAttribute ("value");

		$output["data"] = $cache["headers"];
		$output["this"] = __CLASS__;
		unset ($cache);

		return $output;
	}
}
class sourceEleitoral extends searchIdentity
{	public function __construct ($anew = false)
	{	// Oh! My Gosh. Do we have a daddy?
		parent::__construct ();
		/*
		// Former step: [1] true for new token,
		// later step: [2] false for new token
		*/
		if ($anew == true)
		{	// It releases a new token whose code must be generated at once before the submit and just
			$this->doIndexation ();
			// The image expected as URI data to place the image source
			$this->doSeal ();
		}
		
		return true;
	}
	public function __destruct ()
	{	try
		{	$output["crc"] = hash_file ("crc32b", __FILE__);
			$output["xml"] = $this->bugFields["xml"] ? libxml_get_errors () : libxml_clear_errors ();
			
			return $output;
		}
		catch (Exception $e)
		{	return array ("response" => "error", "status" => $e->getCode (), "desc" => $e->getMessage (), "callback" => []);
		}
	}
	protected $bugFields = ["xml" => true];
	protected $mapFields = ["viewState" => "javax.faces.ViewState", "htmlToken" => "org.apache.struts.taglib.html.TOKEN"];
	protected $wwwFields = ["Host: apps.tse.jus.br", "Origin: http://apps.tse.jus.br"];
	public $sesStorage = [];
	public static function __getOutput ()
	{	return (isset ($_SESSION[__CLASS__]) ? $_SESSION[__CLASS__] : []);
	}
	public function __getCookie ()
	{	$cookie = "";
	
		if (isset ($_SESSION[__CLASS__]["setSession"]))
		foreach ($_SESSION[__CLASS__]["setSession"] as $item => $value)
		$cookie .= ("{$item}={$value}; ");
		
		return trim ($cookie);
	}
	public function __putSession ($entry = "")
	{	if (preg_match ('/Set-Cookie: (.*)\r\n/mi', $entry, $cache)) $entities = (isset ($cache)) ? preg_split ("/[;\s]+/", $cache[1]) : [];
		else $entities = (isset ($entry)) ? preg_split ("/[;\s]+/", $entry) : [];

		if (isset ($entities) && count ($entities))
		foreach ($entities as $entity)
		{	$split = preg_split ("/\=/", $entity);
			$dataset[$split[0]] = $split[1];
			unset ($split);
		}
		else
		$dataset = [];

		if (isset ($_SESSION[__CLASS__]["setSession"]))
		$_SESSION[__CLASS__]["setSession"] = array_merge ($_SESSION[__CLASS__]["setSession"], $dataset);
		else
		$_SESSION[__CLASS__]["setSession"] = $dataset;

		return $dataset;
	}
	protected function doIndexation ()
	{	$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, "http://apps.tse.jus.br/certidaoquitacao/consultaLocalVotacaoNome.do?dispatcher=exibirConsultaLocalVotacaoNome&validate=false");
		curl_setopt ($ch, CURLOPT_HEADER, true);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_NOBODY, false);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $this->wwwFields);
		curl_setopt ($ch, CURLOPT_ENCODING, "gzip, deflate");
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0");
		curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_CAINFO, getcwd()."\general.pem");
		curl_setopt ($ch, CURLOPT_COOKIE, $this->__getCookie ());

		$output["body"] = curl_exec ($ch);
		$output["head"] = curl_getinfo ($ch, CURLINFO_HEADER_OUT);
		$output["error"] = curl_error ($ch);
		curl_close ($ch);
		
		$cache["headers"] = array ();
		$cache["dom"] = new \DOMDocument ();
		$cache["dom"]->loadHTML ($output["body"]);
		$cache["trest"] = $cache["dom"]->getElementsByTagName ("input");
		foreach ($cache["trest"] as $el)
			if (in_array ($el->getAttribute ("name"), $this->mapFields))
				$_SESSION[__CLASS__][array_search ($el->getAttribute ("name"), $this->mapFields)] = $cache["headers"][array_search ($el->getAttribute ("name"), $this->mapFields)] = $el->getAttribute ("value");
		if (preg_match ('/Set-Cookie: (.*)\r\n/mi', $output["body"], $cache["jsession"]))
			$this->__putSession ($output["body"]);

		$output["data"] = $cache["headers"];
		$output["this"] = __CLASS__;

		unset ($cache);	
		return $output;
	}
	protected function doSeal ()
	{	$url = "http://apps.tse.jus.br/certidaoquitacao/captcha.jpg;jsessionid=".$_SESSION[__CLASS__]["setSession"]["JSESSIONID"];
		$wwwFields = ["Host: apps.tse.jus.br",
		// "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0",
		"Accept: image/png,image/*;q=0.8,*/*;q=0.5",
		"Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
		// "Accept-Encoding: gzip, deflate",
		"Referer: http://apps.tse.jus.br/certidaoquitacao/consultaLocalVotacaoNome.do?dispatcher=exibirConsultaLocalVotacaoNome&validate=false",
		"Connection: keep-alive"];

		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, false);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_NOBODY, false);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $wwwFields);
		curl_setopt ($ch, CURLOPT_ENCODING, "gzip, deflate");
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0");
		curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_CAINFO, getcwd()."\general.pem");
		curl_setopt ($ch, CURLOPT_COOKIE, $this->__getCookie ());

		$output["body"] = curl_exec ($ch);
		$output["head"] = curl_getinfo ($ch, CURLINFO_HEADER_OUT);
		$output["error"] = curl_error ($ch);
		$output["close"] = curl_close ($ch);
		
		if (preg_match ('/Set-Cookie: (.*)\r\n/mi', $output["body"], $cache["jsession"]))
			$this->__putSession ($output["body"]);
		
		$cache["headers"] = $_SESSION[__CLASS__]["imageCaptcha"] = parent::__getDataURI ($output["body"]);

		$output["data"] = $cache["headers"];
		$output["this"] = __CLASS__;
		unset ($cache);

		return $output;
	}
	public function doJutsu ($entry)
	{	$url = "http://apps.tse.jus.br/certidaoquitacao/consultaLocalVotacaoNome.do;jsessionid={$entry["jsessionid"]}";
		$post = "org.apache.struts.taglib.html.TOKEN=".urlencode($entry["token"])."&dispatcher=consultarLocalVotacaoNome&validate=true&nomeEleitor=".urlencode($entry["person"])."&dataNascimento=".urlencode($entry["born"])."&nomeMae=".urlencode($entry["mommy"])."&codigoCaptcha=".urlencode($entry["captcha"])."&Consultar=Consultar";
		$wwwFields = ["Host: apps.tse.jus.br",
		"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0",
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
		"Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
		"Accept-Encoding: gzip, deflate",
		"Referer: http://apps.tse.jus.br/certidaoquitacao/consultaLocalVotacaoNome.do?dispatcher=exibirConsultaLocalVotacaoNome&validate=false",
		"Connection: keep-alive",
		"Content-Type: application/x-www-form-urlencoded"];

		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, false);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_NOBODY, false);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 2);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $wwwFields);
		curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt ($ch, CURLOPT_COOKIE, $this->__getCookie ());
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);

		$output["body"] = curl_exec ($ch);
		$output["head"] = curl_getinfo ($ch, CURLINFO_HEADER_OUT);
		$output["error"] = curl_error ($ch);
		$output["close"] = curl_close ($ch);

		$output["data"] = $cache["headers"] = "";
		$output["this"] = __CLASS__;
		unset ($cache);

		return $output;
	}
}
}
?>