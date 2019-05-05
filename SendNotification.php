<?php 
	
function api($title,$body,$to,$authkey)
{
	$url = "https://fcm.googleapis.com/fcm/send";

	$ch	= curl_init($url);
	$header = array();
	$header[] = 'Content-type: application/json';
	$header[] = 'Authorization: key=' . $authkey;


	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	$data = [
	  'to' => $to,
	  'notification' => [
		'title' => $title,
		'body' => $body
	  ]
	];

	curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch, CURLOPT_POST,true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	echo $ch;
	$res = curl_exec($ch);
	echo "<pre><code>".$res."</pre></code>";
	$err = curl_error($ch);
	curl_close($ch);
	if ($err) 
	{
		echo "cURL Error #:" . $err; die();
	} 
}


if(isset($_POST['submit']) && $_POST['submit']=="1")
{
	$authkey="AAAAiMVyMTg:APA91bHR8sFvG509lmNgcvaqdM6G9yTf0IdFtuxlvqY1oBnAqbqtjoOHn2j5Zd8lvTaxap8Gs8j9Zpl3GwAB-qMZ7xmG2X8E988DV8WcG-Kk7FF0G-tu5ksPZwP172MuN_LO6m6PpA5g";
	$to=$_POST['to'];
	$title=$_POST['title'];
	$body=$_POST['body'];

	api($title,$body,$to,$authkey);

}
else
{
?> 
	No Parameters
<?php 
}
?>