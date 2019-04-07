<?php 
	
function api($url,$title,$body,$to,$authkey)
{
	$ch	= curl_init($url);
	$header = array();
	$header[] = 'Content-type: application/json';
	$header[] = 'Authorization: key=' . $authkey;
	$url = 'https://fcm.googleapis.com/fcm/send';


	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	$data = [
	  'to' => $to,
	  'notification' => [
		'title' => $title,
		'body' => $body
	  ]
	];
	curl_setopt($ch, CURLOPT_URL, $url);
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

	api("https://fcm.googleapis.com/fcm/send",$title,$body,$to,$authkey);

echo '<br/><br/><br/><a href="SendNotification.php">Click here to go back</a>';
}
else
{
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <title>API Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <form method="post" >
    <div class="form-group">

<br/><br/><br/>      <label for="authkey">Auth Key:</label>
      <textarea class="form-control" id="authkey" placeholder="Auth Key" name="authkey" >AAAAiMVyMTg:APA91bHR8sFvG509lmNgcvaqdM6G9yTf0IdFtuxlvqY1oBnAqbqtjoOHn2j5Zd8lvTaxap8Gs8j9Zpl3GwAB-qMZ7xmG2X8E988DV8WcG-Kk7FF0G-tu5ksPZwP172MuN_LO6m6PpA5g</textarea>
    </div>
    <div class="form-group">
      <label for="to">To</label>
      <textarea style="height:70px;" class="form-control" id="to" placeholder="To" name="to">db9fGbg-KFQ:APA91bF2P3C0ewLOu2pimWMe4tcbhyxkxkgrlzimRoNzvebLKCkmAn4D26Y11M8m29kOcc_jclHc2SX7jaQHypoRDXGSL_llCxw02Rk2heBnF_AVFywk11BBCAe0qJGdUgm3u5mZnhRj</textarea>
    </div>
    <div class="form-group">
      <label for="title">Title</label>
      <input type="text" class="form-control" id="title" placeholder="Title" name="title" value="Portugal vs. Denmark">
    </div>
    <div class="form-group">
      <label for="body">Body</label>
      <input type="text" class="form-control" id="body" placeholder="Body" name="body" value="5 to 1">
    </div>
   
    <input type="hidden" value="1" name="submit">
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
</div>

</body>
</html>

<?php }