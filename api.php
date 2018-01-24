
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
	

	//var_dump($messages);

	if ($verb == "GET") {

		if (isset($_GET['lastid']) and isset($_GET['mykey'])) {

			$getMessage = $messages[$_GET['lastid']];
			response($getMessage[0], $getMessage[1], $getMessage[2]);

		} elseif (!isset($_GET['lastid']) and isset($_GET['mykey'])) {

			$idlist = "";

			for ($ite = 0 ; $ite < count($messages) ; $ite++) {

				if ($messages[$ite][1] == $_GET['mykey']) {

					$idlist = $idlist . $messages[$ite][0] . ",";

				}

			}
			$idlist = substr($idlist, 0, -1);
			echo($idlist);

		} else {

			http_response_code(400);

		}

	} elseif ($verb == "PUT") {

		if (isset($_GET['mykey']) and isset($_GET['value'])) {

			$newMessage = array($i, $_GET['mykey'], $_GET['value']);
			
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

/*
	if($verb == "GET") {
		if(isset($_GET['action']) and isset($_GET['mykey'])) {
			$name = $_GET['mykey'];
			response($name);
		} else {
			echo "error: please enter required parameters";
		}
	} elseif ($verb == "PUT") {
		$handle = fopen($my_file, 'w');
		$data = 'This is the data as well';
		fwrite($handle, $data);
		$handle = fopen($my_file, 'r');
		response(fread($handle,filesize($my_file)));
	} else {
		echo "error: verb unknown";
	}

	function response($name) {
		header("HTTP/1.1 ");
		
		$Response = $name;
		
		$json_response = $Response;
		echo $json_response;
	}
*/
?>