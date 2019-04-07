<?php
define('API_ACCESS_KEY','AAAAiMVyMTg:APA91bHR8sFvG509lmNgcvaqdM6G9yTf0IdFtuxlvqY1oBnAqbqtjoOHn2j5Zd8lvTaxap8Gs8j9Zpl3GwAB-qMZ7xmG2X8E988DV8WcG-Kk7FF0G-tu5ksPZwP172MuN_LO6m6PpA5g');
 $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
 $token='dd2eFAZsRG0:APA91bGsdfPEXPk_6_NeaLfED6pTW-2emseapq-9G2isO44r3NNdz1FciiSyGWWHVFn6Z5KzKwgxi5uGdAyrL2XPQyHsOZFWgulzsKEyXyT38tU6--gE9RIvcZYDugJ1B2ctntH_l8It';

     $notification = [
            'title' =>'PHP Message',
            'body' => 'From my localserver'
        ];
        $extraNotificationData = ["message" => $notification,"moredata" =>'Some Data'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        ];


		/*
		curl -X POST -H "Authorization: key=AAAAiMVyMTg:APA91bHR8sFvG509lmNgcvaqdM6G9yTf0IdFtuxlvqY1oBnAqbqtjoOHn2j5Zd8lvTaxap8Gs8j9Zpl3GwAB-qMZ7xmG2X8E988DV8WcG-Kk7FF0G-tu5ksPZwP172MuN_LO6m6PpA5g" -H "Content-Type: application/json" -d '{
		  "notification": {
			"title": "Portugal vs. Denmark",
			"body": "5 to 1"
		  },
		  "to": "dd2eFAZsRG0:APA91bGsdfPEXPk_6_NeaLfED6pTW-2emseapq-9G2isO44r3NNdz1FciiSyGWWHVFn6Z5KzKwgxi5uGdAyrL2XPQyHsOZFWgulzsKEyXyT38tU6--gE9RIvcZYDugJ1B2ctntH_l8It"
		}' "https://fcm.googleapis.com/fcm/send"
		*/



		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n\t\t  \"notification\": {\n\t\t\t\"title\": \"Portugal vs. Denmark\",\n\t\t\t\"body\": \"5 to 1\"\n\t\t  },\n\t\t  \"to\": \"dd2eFAZsRG0:APA91bGsdfPEXPk_6_NeaLfED6pTW-2emseapq-9G2isO44r3NNdz1FciiSyGWWHVFn6Z5KzKwgxi5uGdAyrL2XPQyHsOZFWgulzsKEyXyT38tU6--gE9RIvcZYDugJ1B2ctntH_l8It\"\n\t\t}");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = 'Authorization: key=AAAAiMVyMTg:APA91bHR8sFvG509lmNgcvaqdM6G9yTf0IdFtuxlvqY1oBnAqbqtjoOHn2j5Zd8lvTaxap8Gs8j9Zpl3GwAB-qMZ7xmG2X8E988DV8WcG-Kk7FF0G-tu5ksPZwP172MuN_LO6m6PpA5g';
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);


        echo $result;
?>