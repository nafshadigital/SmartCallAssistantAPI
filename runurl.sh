curl -X POST -H "Authorization: key=AAAAiMVyMTg:APA91bHR8sFvG509lmNgcvaqdM6G9yTf0IdFtuxlvqY1oBnAqbqtjoOHn2j5Zd8lvTaxap8Gs8j9Zpl3GwAB-qMZ7xmG2X8E988DV8WcG-Kk7FF0G-tu5ksPZwP172MuN_LO6m6PpA5g" -H "Content-Type: application/json" -d '{
  "notification": {
	"title": "Portugal vs. Denmark",
	"body": "5 to 1"
  },
  "to": "db9fGbg-KFQ:APA91bF2P3C0ewLOu2pimWMe4tcbhyxkxkgrlzimRoNzvebLKCkmAn4D26Y11M8m29kOcc_jclHc2SX7jaQHypoRDXGSL_llCxw02Rk2heBnF_AVFywk11BBCAe0qJGdUgm3u5mZnhRj"
}' "https://fcm.googleapis.com/fcm/send"