<?php

	header("Content-Type:application/json");
	$verb = $_SERVER['REQUEST_METHOD'];

	$dsn = 'mysql:host=127.0.0.1;dbname=blogdb';
	$user_name = 'root';
	$pass_word = "";

	if ($verb == 'GET') {
		//returns message of specific category
		if (isset($_GET['getcategories'])) {

			echo JSON_encode(get_categories_from_database());

		//checks if the login is correct
		} elseif (isset($_GET['username']) and isset($_GET['password'])) {

			http_response_code(200);

			$provided_username = $_GET['username'];
			$provided_password = $_GET['password'];

			$credentials = get_blogger_credentials_from_database();

			if ($provided_username == $credentials['username'] and $provided_password == $credentials['password']) {

				echo true;

			} else {

				echo false;

			}

		// Gets all messages from database with specific category
		} elseif (isset($_GET['category'])) {

			http_response_code(200);

			$messages = get_messages_from_database_by_category($_GET['category']);

			$response = array();

			foreach ($messages as $row) {

				$response[] = array($row['id'], $row['category'], $row['message']);

			}

			echo json_encode($response);

		} else {

			http_response_code(200);
			echo get_all_messages_from_API();

		}

	} elseif ($verb == 'POST') {

		if (isset($_GET['categories']) and (isset($_GET['message']))) {

			http_response_code(200);
			write_message_to_database($_GET['categories'], $_GET['message']);

		} elseif (isset($_GET['category'])) {

			http_response_code(200);
			write_category_to_database($_GET['category']);

		} else {

			http_response_code(400);

		}

	} else {

		http_response_code(400);

	}

	function get_all_messages_from_API() {

		global $dsn;
		global $user_name;
		global $pass_word;

		$connection = new PDO($dsn, $user_name, $pass_word);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT b.id, b.message, c.category FROM blogposts b, categories c, blogpost_categories bc WHERE bc.category_id = c.id AND bc.blogpost_id = b.id";

		$result = $connection->query($sql);

		$response = array();

		foreach ($result as $row) {
			$response[] = array($row['id'], $row['category'], $row['message']);
		}

		$json_response = json_encode($response);

		return $json_response;

		$connection = null;

	}

	function write_message_to_database($categories, $message) {

		global $dsn;
		global $user_name;
		global $pass_word;

		$connection = new PDO($dsn, $user_name, $pass_word);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$category_id_list = get_category_id($categories);

		//var_dump($category_id_list);

		$sql = "INSERT INTO blogposts (message) " . "VALUES ('$message')";
		//echo $message . " added to database";
		$connection->exec($sql);

		$sql = "SELECT blogposts.id FROM blogposts";

		$result = $connection->query($sql);

		$ids=[];

		foreach ($result as $row) {
			$ids[] = $row['id'];
		}

		$last_id = $ids[count($ids) - 1];

		for ($i = 0 ; $i < count($category_id_list) ; $i++) {
			$sql = "INSERT INTO blogpost_categories (blogpost_id, category_id) " . "VALUES ('$last_id', '$category_id_list[$i]')";
			$connection->exec($sql);
		}

		$connection = null; // Close connection

	}

	function write_category_to_database($category) {

		global $dsn;
		global $user_name;
		global $pass_word;

		$connection = new PDO($dsn, $user_name, $pass_word);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try {
			$sql = "INSERT INTO categories (category) " . "VALUES ('$category')";
			$connection->exec($sql);
			echo $category . " added to database";
		}
		catch(PDOException $e) {
			echo $sql . "<br>" . $e->getMessage();
		}

		$connection = null; // Close connection

	}

	function get_categories_from_database() {

		global $dsn;
		global $user_name;
		global $pass_word;

		$connection = new PDO($dsn, $user_name, $pass_word);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT * FROM categories";

		$result = $connection->query($sql);

		$categories = [];

		foreach ($result as $row) {

			$categories[] = array($row['id'], $row['category']);

		}

		return $categories;

	}

	function get_category_id($categories) {

		$category_list = get_categories_from_database();

		$category_id_list = [];

		$categories = explode(",", $categories);

		for ($i = 0 ; $i < count($category_list) ; $i++) {
			for ($j = 0 ; $j < count($categories) ; $j++) {
				if ($categories[$j] == $category_list[$i][1]) {
					$category_id_list[] = $category_list[$i][0];
				}
			}
		}

		return $category_id_list;
	}

	// Gets blogger credentials from the database
	function get_blogger_credentials_from_database() {

		global $dsn;
		global $user_name;
		global $pass_word;

		$connection = new PDO($dsn, $user_name, $pass_word);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT * FROM bloggercredentials where id = 1";

		$result = $connection->query($sql);

		foreach ($result as $row) {

			$credentials = array(

				"username" => $row['username'],
				"password" => $row['password']

			);	

		}

		return $credentials;

	}

	function get_messages_from_database_by_category($category) {
		global $dsn;
		global $user_name;
		global $pass_word;

		$connection = new PDO($dsn, $user_name, $pass_word);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT b.id, c.category, b.message FROM blogposts b, categories c, blogpost_categories bc 
				where bc.blogpost_id = b.id AND bc.category_id = c.id AND
				c.category = '$category'";

		return $connection->query($sql);
	}
?>