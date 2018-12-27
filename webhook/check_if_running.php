<?php

exec("ps -U vicheanak -u vicheanak", $output, $result);
$is_found = 'false';
foreach ($output AS $line) {
	if(strpos($line, "konleng.php")){
		$is_found = 'true';	
	} 
}
// echo $is_found;
if ($is_found == 'false'){
	exec("php /var/www/khmernewslive/webhook/konleng.php >/var/www/khmernewslive/webhook/konleng.log &", $output1);
	// exec("ls", $output1);
	// print_r($output1);
}

?>