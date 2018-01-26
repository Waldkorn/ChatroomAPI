
<?PHP

	header("Content-Type:application/json");

	$verb = $_SERVER['REQUEST_METHOD'];
	$my_file = 'file.txt';

	if ($verb == "GET") {

		
		if (isset($_GET['minimumid']) and isset($_GET['key'])) {

			$messages = array();
			$messageIds = askIdsFromDatabase($_GET['key'], $_GET['minimumid']);
			$messageIds = json_encode($messageIds);
			echo $messageIds;

		} else {

			http_response_code(400);

		} 

	} elseif ($verb == "PUT") {

		if (isset($_GET['key']) and isset($_GET['message'])) {
			
			writeMessageToDatabase($_GET['key'], $_GET['message']);
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
	echo fread($handle,filesize($file));
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

	$jsonResponse = json_encode($response);
	
	return $response;
}

function writeMessageToDatabase($mykey, $message) {

	$dsn = 'mysql:dbname=chatdb;host=127.0.0.1';
	$user_name = 'root';
	$pass_word = "";

	$connection = new PDO($dsn, $user_name, $pass_word);
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	try {
	$sql = "INSERT INTO messages (mykey, message) " .
	"VALUES ('$mykey', '$message')";
	$connection->exec($sql);
	echo $message . " added to database";
	}
	catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
	}
	$connection = null; // Close connection
}

function askIdsFromDatabase($mykey, $minimumid) {

	$dsn = 'mysql:dbname=chatdb;host=127.0.0.1';
	$user_name = 'root';
	$pass_word = "";

	$connection = new PDO($dsn, $user_name, $pass_word);
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * FROM messages where mykey = '$mykey'";

	$result = $connection->query($sql);

	$response = [];

	foreach ($result as $row) {
		$placeholder = array($row['id'], $row['mykey'], $row['message']);
		if ($placeholder[0] > $minimumid) {
			$response[] = $placeholder;
		}
	}

	return $response;
}

function askMessageFromDatabase($mykey, $id) {

	$dsn = 'mysql:dbname=chatdb;host=127.0.0.1';
	$user_name = 'root';
	$pass_word = "";

	$connection = new PDO($dsn, $user_name, $pass_word);
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * FROM messages where mykey = '$mykey' and id = '$id'";

	$result = $connection->query($sql);

	$response = [];

	foreach ($result as $row) {
		$placeholder = array($row['id'], $row['mykey'], $row['message']);
		$response = $placeholder;
	}

	return $response;

	$connection = null;

}

?>