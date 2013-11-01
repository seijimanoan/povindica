<?php

/*
Copyright 2013 de SEIJI MANOAN SEO
Este arquivo é parte do programa POVINDICA BRASIL. O POVINDICA BRASIL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da [GNU General Public License OU GNU Affero General Public License] como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença. Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a licença para maiores detalhes. Você deve ter recebido uma cópia da [GNU General Public License OU GNU Affero General Public License], sob o título "LICENCA.txt", junto com este programa, se não, acesse http://www.gnu.org/licenses/
*/

	if (defined ('DEMETER') == false || DEMETER !== "federalst") die (header ("HTTP/1.1 404 Not Found"));
	class Replier
	{	public $modular = -1;
		public $method = false;
		public $query = "";
		public $account = false;
		public $error = false;
		public $salt = "olorun";
		public $functions = array ();
		public $database = false;
		public $setting = array ();
		public function __construct ($modular = 0, $method = "post", $access = null)
		{	if (file_exists ("./database.php"))
			{	require_once "./database.php";
				$this->setting = array (SERVIDOR, USUARIO, SENHA, BANCO);
			}
		
			if (is_array ($access)) $this->setting = $access;
			if (is_resource ($access)) $this->database = $access;
		
			$this->modular = $modular;
			$this->method = $method == "get" ? $_GET : $_POST;
			
			$this->__connect ();
			$this->functions = get_class_methods ($this);
			$this->__router ();
		}
		public function __destruct ()
		{
			if (is_object ($this->database)) $this->database->close ();
		}
		private function __connect ()
		{
			$this->database = new \mysqli ($this->setting[0], $this->setting[1], $this->setting[2], $this->setting[3]);
		}
		public function __matrix ($entry)
		{
			if (is_object ($entry))
			{	if (function_exists ("mysqli_fetch_all")) $rels = $entry->fetch_all (MYSQLI_ASSOC);
				else while ($rel = $entry->fetch_array (MYSQLI_ASSOC)) $rels[] = $rel;
				return $rels;
			}
			else return false;
		}
		private function __checkout ($string)
		{	// $string = filter_var ($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$string = preg_replace ('/\s+/', ' ', $string);
			$string = trim ($string);
			$string = get_magic_quotes_gpc () ? stripslashes ($string) : $string;
			$string = $this->database->real_escape_string ($string);
			return $string;
		}
		private function __router ()
		{
			/*
			// Come on escape all the query strings over the environment to avoid SQL injections
			*/
			// $this->method = array_map (function ($t){ return $this->database->real_escape_string (urldecode ($t)); }, $this->method);
			$this->method = array_map (function ($t){ return $this->__checkout (urldecode ($t)); }, $this->method);
			/*
			// So here we will receive its Module and Action
			*/
			@$this->query = $this->method["go"];
			@$this->account = $this->method["on"];

			switch ($this->query)
			{
				case 'json':
					foreach ($this->functions as $func)
					if (substr ($func, 0, 1) === substr ($this->query, 0, 1) && substr ($func, 2) === $this->account)
					{	$authed = $this->__auth ();
						$response = (($authed["response"] == "success" && $this->account !== "getLogon") || $this->account === "getLogin") ? $this->{$func}() : $authed;
						array_walk_recursive ($response, array ($this, "__encoding"));
						echo json_encode ($response);
					}
					break;
				default:
					echo json_encode (array ("response" => "error", "status" => 0, "desc" => "What's the output format you are looking for?"));
					break;
			}
		}
		public function __encoding (&$item, $key)
		{	// If it is ARRAY then we clear its empty text elements
			if (is_array ($item)) $item = array_filter ($item, "strlen");
			// If it is STRING but it isn't ENCODED then we will
			if (is_string ($item)) if (!preg_match ('!!u', $item)) $item = utf8_encode ($item);
		}
		private function j_getLogin ()
		{	if (isset ($_SESSION["dados"])) unset ($_SESSION["dados"]);
			if (isset ($_SERVER["HTTP_COOKIE"])) unset ($_SERVER["HTTP_COOKIE"]);

			$user = isset ($this->method["resu"]) ? $this->method["resu"] : false;
			$pass = isset ($this->method["ssap"]) ? md5 ($this->__rc4 (md5 ($this->salt.$_COOKIE["PHPSESSID"]), $this->method["ssap"])) : false;

			$out = $this->database->query ("SELECT nome, login, uid, email, nivel_acesso FROM usuarios WHERE login = '".$user."' AND senha = '".$pass."' LIMIT 1;")->fetch_assoc ();
			$knock = $out ? true : false;

			if (isset ($knock) && is_array ($out))
			{
				$banned = $this->database->query ("(SELECT what FROM demperms WHERE whom = '".$out["login"]."' AND what = ".$this->modular." AND banned = 1 AND type = 'user') UNION (SELECT what FROM demperms WHERE whom = ".$out["nivel_acesso"]." AND what = ".$this->modular." AND banned = 1 AND type = 'group')")->fetch_array (MYSQLI_ASSOC);
				$allowed = $this->database->query ("(SELECT what FROM demperms WHERE whom = '".$out["login"]."' AND what = ".$this->modular." AND banned = 0 AND type = 'user') UNION (SELECT what FROM demperms WHERE whom = ".$out["nivel_acesso"]." AND what = ".$this->modular." AND banned = 0 AND type = 'group')")->fetch_array (MYSQLI_ASSOC);

				$is_banned = is_array ($banned) ? true : false;
				$is_allowed = is_array ($allowed) ? true : false;

				if ($this->modular == 0 || ($is_banned == false && $is_allowed == true)) $_SESSION["dados"] = $out;
				else return array ("response" => "error", "status" => 81, "desc" => "Maybe you do not have the required user level, neither do leadership profile.");
			}
			else return array ("response" => "error", "status" => 81, "desc" => "Did you type your credential properly?", "data" => md5 ($this->salt.$_COOKIE["PHPSESSID"]));
			$_SESSION["dados"]["sess"] = $_COOKIE["PHPSESSID"];
			// setcookie ("PHPSESSID", session_id (), 0, "/", false, false);
			// header ("Set-Cookie: PHPSESSID=".session_id()."; path=/; httpOnly;");
			// setcookie (session_name (), $_COOKIE[session_name()], 0, "/", 1, 1);
			// setcookie ("PHPSESSID", session_id (), 0, "/", 1, 1);
			
			
			return array ("response" => "success", "status" => 2000, "desc" => "Be welcome @".$_SESSION["dados"]["login"]."!");
		}
		private function __auth ()
		{	return call_user_func_array (array ($this, "j_getLogon"), func_get_args ());
		}
		private function j_getLogon ()
		{	if (empty ($_COOKIE["PHPSESSID"])) $_COOKIE["PHPSESSID"] = session_id ();
			setcookie (session_name (), $_COOKIE["PHPSESSID"], 0, "/", "", ($_SERVER["SERVER_NAME"] == "localhost" ? false : true), true);

			if (isset ($_SESSION["dados"])) $sess = $_SESSION["dados"];
			/* Deprecated since we do not need cookie anymore due to the enabled session
			// else if (isset ($_SERVER["HTTP_COOKIE"]) && strpos ($_SERVER["HTTP_COOKIE"], "%")) $sess = (strpos ($_SERVER["HTTP_COOKIE"], "%") || strpos ($_SERVER["HTTP_COOKIE"], "=")) ? unserialize (substr (urldecode ($_SERVER["HTTP_COOKIE"]), strpos (urldecode ($_SERVER["HTTP_COOKIE"]), "=")+1)) : false;
			*/
			else return array ("response" => "error", "status" => 81, "desc" => "No logon.", "data" => md5 ($this->salt.$_COOKIE["PHPSESSID"]));

			preg_match ("/PHPSESSID=([a-z0-9]*);?/m", $_SERVER["HTTP_COOKIE"], $sync);

			$banned = $this->database->query ("(SELECT what FROM demperms WHERE whom = '".$sess["login"]."' AND what = ".$this->modular." AND banned = 1 AND type = 'user') UNION (SELECT what FROM demperms WHERE whom = ".$sess["nivel_acesso"]." AND what = ".$this->modular." AND banned = 1 AND type = 'group')")->fetch_array (MYSQLI_ASSOC);
			$allowed = $this->database->query ("(SELECT what FROM demperms WHERE whom = '".$sess["login"]."' AND what = ".$this->modular." AND banned = 0 AND type = 'user') UNION (SELECT what FROM demperms WHERE whom = ".$sess["nivel_acesso"]." AND what = ".$this->modular." AND banned = 0 AND type = 'group')")->fetch_array (MYSQLI_ASSOC);

			$is_banned = is_array ($banned) ? true : false;
			$is_allowed = is_array ($allowed) ? true : false;

			$lead = ($this->modular == 0 || ($is_banned == false && $is_allowed == true)) ? "yes" : "no";

			if (isset ($sess) && is_array ($sess) && $sess["sess"] == $sync[1] && $lead == "yes") return array ("response" => "success", "status" => 2000, "desc" => "Authenticated! You own the right leadership: {$lead}!", "data" => md5 ($this->salt.$sess["sess"]));
			else return array ("response" => "error", "status" => 81, "desc" => "Logon error. You own the right level: ".$lead."!", "data" => md5 ($this->salt.$sess["sess"]));
		}
		private function j_getLogout ()
		{	if (isset ($_SESSION["dados"])) unset ($_SESSION["dados"]);
			if (isset ($_SERVER["HTTP_COOKIE"])) unset ($_SERVER["HTTP_COOKIE"]);
			session_regenerate_id ();
			return array ("response" => "success", "status" => 2000, "desc" => "Logged off.");
		}
		public function __rc4 ($key_str, $data_str)
		{	$data_str = strlen ($data_str) > 0 ? base64_decode ($data_str) : false;
		
			if ($data_str == false)
			return false;
		
			$key = array ();
			$data = array ();
			for ($i = 0; $i < strlen ($key_str); $i++) $key[] = ord ($key_str{$i});
			for ($i = 0; $i < strlen ($data_str); $i++) $data[] = ord ($data_str{$i});

			for ($c = 0; $c < 256; $c++) $state[$c] = $c;

			$len = count ($key);
			$index1 = $index2 = 0;
			for ($counter = 0; $counter < 256; $counter++)
			{	$index2   = ($key[$index1] + $state[$counter] + $index2) % 256;
				$tmp = $state[$counter];
				$state[$counter] = $state[$index2];
				$state[$index2] = $tmp;
				$index1 = ($index1 + 1) % $len;
			}

			$len = count ($data);
			$x = $y = 0;
			for ($counter = 0; $counter < $len; $counter++)
			{	$x = ($x + 1) % 256;
				$y = ($state[$x] + $y) % 256;
				$tmp = $state[$x];
				$state[$x] = $state[$y];
				$state[$y] = $tmp;
				$data[$counter] ^= $state[($state[$x] + $state[$y]) % 256];
			}

			$data_str = "";
			for ($i = 0; $i < $len; $i++) $data_str .= chr ($data[$i]);
			return $data_str;
		}
		public function __filterHeader ($data, $extras = [])
		{	$alias = $this->__filterAliasesTable;
			foreach ($data as $ttk => $ttv) foreach ($alias as $ppk => $ppv) if (in_array ($ttk, $ppv)) $anew[($ttk==$ppv["out"]?$ppv["in"]:$ppv["out"])] = is_string ($ttv) ? /*utf8_encode*/ (($ttk==$ppv["out"]?$this->__checkout ($ttv):$ttv)) : $ttv;
			$anew = array_merge_recursive ($anew, $extras);
			return $anew;
		}
		private $__filterAliasesTable =
		[	"nome" => ["in" => "nome", "out" => "name"],
		];
		public function __filterSplitIntoTable ($resource, $extras = [])
		{	$rpt["cols"] = implode (",", array_keys (array_merge ($resource, $extras)));
			$rpt["rows"] = implode (",", array_map (function ($z){ return ((is_numeric ($z) || (is_string($z)?($z=="NOW()"?true:false) : false) || (is_array ($z)?(($z=implode(";",$z))?false:false):false)) ? $z : "'".utf8_decode ($z)."'"); }, array_values (array_merge ($resource, $extras))));
			return $rpt;
		}
		public function __filterSideBySide ($data)
		{	foreach ($data as $key => $val) $holder[$key] = "{$key}=".(is_numeric ($val) ? $val : (is_array ($val) ? "'".implode (";", $val)."'" : (strpos ($val, "(") ? $val : "'".utf8_decode ($val)."'")));
			$holder = implode (",", $holder);
			return $holder;
		}
	}

?>