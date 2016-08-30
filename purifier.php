<?php
	class Purify {
		
		private $basic_tags="<p><br /><br/><br><a><b><strong><em><i><ul><ol><li><blockquote>";
		
		private $total_tags="";
		
		private $authorized_url=null; //URI autorisées sur un src ou un href
		
		private $verbose=false;
		
		private $authorize_scripts=false;
		
		function __construct($authorized_tags="BASIC",$other_tags="",$verbose=false){
			
			if($verbose){
				echo "Verbose mod activated <br/>";
				$this->verbose=true;
			}
			
			if($authorized_tags=="BASIC"){
				$this->total_tags=$this->basic_tags;
				if($this->verbose===true){
					echo "Configured BASIC<br/>";
				}
				}else if(($authorized_tags=="NONE"||$authorized_tags=="")&&strlen($other_tags)>2){
				if($this->verbose===true){
					echo "Configured NONE<br/>";
				}
			}
			
			if(strlen($other_tags)>2){
				$this->total_tags=$this->basic_tags.$other_tags;
				if($this->verbose===true){
					echo "Total authorized tags: ".htmlentities($this->total_tags)."<br/>";
				}
			}
			
		}
		
		public function setAuthorizedUrl($authorized_url){
			if(strlen($authorized_url)>9){
				$arr=explode(",",$authorized_url);
				$this->authorized_url=array();
				foreach($arr as $url){
					if(strlen($url)>9){
						if($this->verbose===true){
							echo "Configured URL. (".$url.")<br/>";
						}
						array_push($this->authorized_url,$url);
					}
				}
			}
		}
		
		public function verifUrl($str){
			if($this->verbose===true){
				echo "URL filter started...<br/>";
			}
			
			$expl_src=explode("src=",$str);
			$final="";
			if(sizeof($expl_src)>=2){
				for($a=1;$a<sizeof($expl_src);$a++){
					if($this->verbose===true){
						echo "ACTUAL : ".$a."<br/>";
					}
					if(isset($expl_src[$a])){
						$ok=false;
						for($i=0;$i<sizeof($this->authorized_url);$i++){
							if(true){
								if(preg_match("#^(\"|')(http:\/\/|https:\/\/|\/\/?)".$this->authorized_url[$i]."(.*?)(\"|')#",$expl_src[$a])){
									$ok=true;
									if($this->verbose===true){
										echo "Allowed URL found : ".htmlentities($expl_src[$a]).", VERIFIED FOR : ".$this->authorized_url[$i]."<br/>";
									}
									break;
									}else{
									if($this->verbose===true){
										echo "Forbidden URL found : ".htmlentities($expl_src[$a])."<br/>";
									}
								}
							}
						}
					}
					if(isset($expl_src[$a])&&$ok===false){
						$expl_src[$a]=preg_replace("#^(\"|')(.*?)(\"|')#","''",$expl_src[$a]);
					}
					if($a==1){
						$final.=$expl_src[$a-1]."src=".$expl_src[$a];
						}else{
						$final.="src=".$expl_src[$a];
					}
				}
				$str=$final;
			}
			
			$expl_href=explode("href=",$str);
			$final="";
			if(sizeof($expl_href)>=2){
				for($a=1;$a<sizeof($expl_href);$a++){
					if($this->verbose===true){
						echo "ACTUAL processing : ".$a."<br/>";
					}
					if(isset($expl_href[$a])){
						$ok=false;
						for($i=0;$i<sizeof($this->authorized_url);$i++){
							if(true){
								if(preg_match("#^(\"|')(http:\/\/|https:\/\/|\/\/?)".$this->authorized_url[$i]."(.*?)(\"|')#",$expl_href[$a])){
									$ok=true;
									if($this->verbose===true){
										echo "Allowed URL found : ".htmlentities($expl_href[$a]).", VERIFIED FOR : ".$this->authorized_url[$i]."<br/>";
									}
									break;
									}else{
									if($this->verbose===true){
										echo "Forbidden URL found ".htmlentities($expl_href[$a]).", VERIFIED FOR : ".htmlentities($expl_href[$a])."<br/>";
									}
								}
							}
						}
					}
					if(isset($expl_href[$a])&&$ok===false){
						$expl_href[$a]=preg_replace("#^(\"|')(.*?)(\"|')#","''",$expl_href[$a]);
					}
					if($a==1){
						$final.=$expl_href[$a-1]."href=".$expl_href[$a];
						}else{
						$final.="href=".$expl_href[$a];
					}
				}
				$str=$final;
			}
			
			if(preg_match("#<script#",$str)){ //Script LINKS
				if($this->verbose){
					echo "INTO SCRIPT of REPLACEMENTS.<br/>";
				}
				if($this->authorize_scripts){
					if($this->verbose){
						echo "CHECKING SCRIPTS.<br/>";
					}
					$patterns=array(
					"#<script(.*?)>((?:\r|\n|.)+)<\/script>#"
					);
					$replacements=array(
					"<script $1></script>"
					);
					$str=preg_replace($patterns,$replacements, $str);
				}else{
					$patterns=array(
					"#<script(.*?)>((?:\r|\n|.)+)<\/script>#"
					);
					$replacements=array(
					""
					);
					$str=preg_replace($patterns,$replacements, $str);
				}
			}
			
			return $str;
		}
		
		public function setAuthorizeScripts($truefalse){
			
			if($truefalse===true){
				$this->authorize_scripts=true;
				if(strpos("<script>",$this->total_tags)===false&&$truefalse===true){
					$this->total_tags=$this->total_tags."<script>";
				}
				}else{
				$this->authorize_scripts=false;
			}
		}
		
		
		/*Sanitises html text from user input fields. */
		public function purify($str = "") {
			if ( ! is_string($str) || strlen($str) < 1) {
				return "";
			}
			
			if($this->authorized_url !== null){ //Filtre les URL
				$str=$this->verifUrl($str);
			}
			
			// Strip tags but allow <p>, <br />, <a>, <strong>, <em>.
			$str = strip_tags($str, $this->total_tags);
			
			// Clean out ''s that strip_text uses around href's
			$str = str_replace( '', '', $str );
			
			// Strip out all paragraphs with attributes
			//$str = preg_replace("/<p[^>]*>/", '<p>', $str);
			
			return $str;
		}
	}
	
	class PurifyBB{
		
		private $purifyConfig=null;
		
		public function setPurifyConfig(Purify $config){
			$this->purifyConfig=$config;
		}
		
		public function verif_url($text,$urls=null){
			if($this->purifyConfig!==null){
				$purify=$this->purifyConfig;
				}else{
				$purify=new Purify("BASIC");
			}
			
			if($urls!==null){
				$purify->setAuthorizedUrl($urls);
				$text=$purify->verifUrl($text);
			}
			
			return $purify->purify($text);
		}
		
		public function bbcode_to_html($text){
			$text=nl2br($text);
			$patterns = array(
            "/\[link\](.*?)\[\/link\]/",
            "/\[url\](.*?)\[\/url\]/",
            "/\[lien=(.*?)\](.*?)\[\/lien\]/",
            "/\[a\](.*?)\[\/a\]/",
            "/\[img\](.*?)\[\/img\]/",
            "/\[b\](.*?)\[\/b\]/",
            "/\[u\](.*?)\[\/u\]/",
            "/\[ul\]((?:\r|\n|.)+)\[\/ul\]/",
            "/\[ol\]((?:\r|\n|.)+)\[\/ol\]/",
            "/\[li\](.*?)\[\/li\]/",
            "/\[i\](.*?)\[\/i\]/"
			);
			$replacements = array(
            "<a href=\"$1\">$1</a>",
            "<a href=\"$1\">$1</a>",
            "<a href=\"$1\">$2</a>",
            "<a href=\"$1\">$1</a>",
            "<img src=\"$1\">",
            "<b>$1</b>",
            "<u>$1</u>",
            "<ul>$1</ul>",
            "<ol>$1</ol>",
            "<li>$1</li>",
            "<i>$1</i>"
			);
			
			$text= preg_replace($patterns,$replacements, $text);
			return $text;
		}
	}
	
?>