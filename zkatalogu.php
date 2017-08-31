<?php
	$dir = "xml/";
	$files = scandir($dir);
	//var_dump($files);
	foreach($files as $f){
		if(!is_file($f)){
			echo "jestem plikem ".$f."<br>";
		}
		// if($f!="." || $f!=".."){
			// echo "<br>".$f;
		// }
	}
?>