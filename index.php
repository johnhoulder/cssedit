<?php
	ini_set('display_errors','on');
	error_reporting(E_WARNING);
	$latest = file_get_contents("http://cssedit.toxic-productions.com/VERSION");
	if(file_get_contents("./VERSION") != $latest){
		echo("NOTICE: Not at current version. This could be an issue.");
	}
	file_put_contents("./list.txt",$_SERVER['REMOTE_ADDR']."=".time()."=".$_SERVER['REQUEST_URI']."\r\n",FILE_APPEND);
?>
<html>
	<head>
		<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script> <!-- Load jQuery base -->
		<title>CSSEdit - View Only</title>
	</head>
	<body>
		<?php
			if(!isset($_GET['file'])){
				$error = "No CSS file defined.";
			}
			$_GET['file'] = preg_replace('%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i', '', $_GET['file']);
			echo("<div id='viewinfo'>Viewing: ".$_GET['file']."</div>");
			if(!file_exists($_GET['file'])){
				$error = "File ({$_GET['file']}) doesn't exist on local server.";
			}
			if(substr($_GET['file'],strlen($_GET['file'])-4) != ".css"){
//			$_GET['file'] = str_replace(array(".php",".phps",".html",".shtml",".htaccess","../","http://","https://",".cgi"),"SECURITYBREACH",$_GET['file']); //DEPRECATED SECURITY
				$error = "Breach of security detected. Attempt logged and reported.";
			}
			if(isset($error)){
				die("<div id='error'>$error</div></body></html>");
			}
			$css = file_get_contents($_GET['file']);
			$css = explode("\r\n",$css);
			$i=0;
			foreach($css as $v){
				$incomment = false;
				if(strpos($v,"/*") > 0){
					$v = substr($v,0,strpos($v,"/*"));
					$incomment = true;
					echo("<a href='#' onClick='\$(\"#commenttag$i\").toggle();'><img src='http://www.webdesigndev.com/wp-content/uploads/2009/08/plus_sign.png' /></a>(Comment) $v");
				}
				if(!$incomment){
					if(!$insub){
						if(substr($v,strlen($v)-1) == "{"){
							$insub = true;
							$subparent = substr($v,0,strlen($v)-1);
							$subparent = str_replace(array("#","."),"",$subparent);
							echo("<a href='#' onClick='\$(\"#{$subparent}tag\").toggle();'><img src='http://www.webdesigndev.com/wp-content/uploads/2009/08/plus_sign.png' /></a>$v");
						}
					}else{
						if(substr($v,strlen($v)-1) == "}"){
							$insub = false;
							$subparent = "";
							echo("</div>}<br>");
						}else{
							echo("<div style='display:hidden;' id='{$subparent}tag'>$v</div>");
						}
					}
				}else{
					echo("<div id='commenttag$i'>$v</div>");
					if(strpos($v,"*/")){
						$incomment = false;
						$i++;
					}
				}
			}
		?>
	</body>
</html>