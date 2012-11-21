<?
require_once dirname(dirname(__FILE__)).'/classes/analyze.php';
$a = new traffix_analyze;

$sql = array('select * from traffix_request_log where analyzed=0');
$new_traffic = $a->select($sql);
foreach($new_traffic as $n) {

    $sql[] = 'update traffix_request_log set analyzed=1 where id=:id';
    $sql[] = array('id'=>$n['id']);
    $a->alter($sql);

    if( $a->warnings($n)!==false ) {
	$sql[] = 'insert into traffix_suspicious_traffic (ip) values (:ip)';
	$sql[] = array('ip'=>$n['ip']);
	$a->alter($sql);
    }
}

$sql = array('select trl.* from traffix_request_log trl, traffix_suspicious_traffic trt where trt.ip=trl.ip');
$suspicious = $a->select($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Traffix - Control Panel - Suspicious Traffic</title>
    <meta charset="utf-8">
    <script src="js/functions.js"></script>    
    <link rel="stylesheet" href="css/traffix.css">
</head>

<body>
    <div id="content">

	<div id="details"></div>

	<?include 'nav.html'?>

        <div class="info_box">
            <span class="info_box_header">Suspicious Traffic</span>
            <span class="info medium_cell">Initial Request</span>
            <span class="info small_cell">IP Address</span>
            <span class="info medium_cell">Reverse DNS</span>
            <span class="info large_cell">User Agent</span>
	    <span class="info small_cell">Details</span><br><br>
            <?
	    foreach( $suspicious as $request ) {
                echo "\n<span class='info medium_cell'>".date('r',$request['request_time'])."</span>\n";
                echo "<span class='info small_cell' title='$request[ip]'>$request[ip]</span>\n";
                echo "<span class='info medium_cell' title='$request[rDNS]'>$request[rDNS]</span>\n";
                echo "<span class='info large_cell' title='".str_replace("'",'"',$request['user_agent'])."'>$request[user_agent]</span>\n";
		echo "<span class='info small_cell'><div class='js_button' onclick='details(\"$request[id]\");'>View</div></span><br><br>\n";
		echo "<div id='headers$request[id]' class='hidden_headers'>$request[request_headers]</div>\n";
		echo "<div id='warnings$request[id]' class='hidden_headers'>".json_encode($a->warnings($request))."</div>\n";
            }
            ?>
        </div>

    </div>
</body>

</html>
