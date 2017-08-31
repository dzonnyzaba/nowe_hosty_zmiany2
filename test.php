<?php
class test{
    public function pobierzPlikiXML(){
        $dir = "xmle";
        $tabtmp = array();
        $files = scandir($dir);
        $ile = count($files);
        foreach($files as $f){
            if($f!="." && $f!=".."){
            if($f=='10_10_0_0-18.xml'){
                $tap = array();
                $tap[0] = basename($f, ".xml");
                $tap[1] = 'dhcpd-vlan10';
                $tabtmp[] = $tap;
            }else{
                $tap = array();
                $tap[0] = basename($f, ".xml");
                $tap[1] = 'dhcpd-vlan64';
                $tabtmp[] = $tap;
            }
        }
		}
        if(count($tabtmp)>=1){
            
                return $tabtmp;
        }else{
                echo "nieeeeeeeeeeeeeeee";
                header('location: index.php?error=1');
                exit();
        }
        var_dump($tabtmp);
    }
	
	
	    public function powysylaj(){
        $r = $this->pobierzPlikiXML();
        if($r){
            foreach ($r as $element){
				var_dump($element);
            }
        }
    }
}
$t = new test();

$t->powysylaj();

//echo $t1[1][1];