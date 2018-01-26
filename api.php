
<?PHP

	header("Content-Type:application/json");

	$verb = $_SERVER['REQUEST_METHOD'];
	$my_file = 'file.txt';
	if (json_decode(openFile($my_file)) != null) {
		$messages = json_decode(openFile($my_file));
		$i = count($messages);
	} else {
		$messages = [];
		$i = 0;
	}

	if ($verb == "GET") {

		
		if (isset($_GET['id']) and isset($_GET['key'])) {

			http_response_code(201);
			$getMessage = $messages[$_GET['id']];
			response($getMessage[0], $getMessage[1], $getMessage[2]);


		} elseif (!isset($_GET['id']) and isset($_GET['key'])) {

			$idlist = "";
			http_response_code(200);

			for ($ite = 0 ; $ite < count($messages) ; $ite++) {

				if ($messages[$ite][1] == $_GET['key']) {

					$idlist = $idlist . $messages[$ite][0] . ",";

				}

			}
			
			$idlist = substr($idlist, 0, -1);
			echo($idlist);

		} else {

			http_response_code(400);

		} 
/*
		if (isset($_GET['minimumid'] and isset($_GET['mykey']))) {

			http_response_code(200);
			$lastid = $_GET['lastid'];

			$sendMessages = array_slice($messages, $lastid);

			return $sendMessages;

		} else {

			http_response_code(400);

		}
*/
	} elseif ($verb == "PUT") {

		if (isset($_GET['key']) and isset($_GET['message'])) {

			$newMessage = array($i, $_GET['key'], $_GET['message']);
			
			array_push($messages, $newMessage);
			writeFile($my_file, $messages);
			http_response_code(200);

			echo("Message stored");

		} else {

			http_response_code(400);

		}

	} else {

		http_response_code(400);

	}

	function openFile($file) {
		$handle = fopen($file, 'r');
		return fread($handle,filesize($file));
	}

	function writeFile($file, $message) {
		$message = json_encode($message);
		$handle = fopen($file, 'w');
		fwrite($handle, $message);
	}

	function response($id, $key, $message) {
		header("HTTP/1.1 ");
		
		$response['message'] = $message;
		$response['id'] = $id;
		$response['key'] = $key;
		
		$json_response = json_encode($response);
		echo $json_response;
	}

?>