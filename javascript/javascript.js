var username = "";
var key = "ewout"
var request = new XMLHttpRequest();
var highestId = -500;

var messageScreen = document.getElementById("message-screen");

function login() {
	username = getUserName();
	key = getKey();

	if (username != "") {

		hideLoginScreen();
		showChatroom();

		window.setInterval(function(){
			//getAllMessageIds();
			messages = JSON.parse(grabMessageById(highestId));
			for (i = 0 ; i < messages.length ; i++) {
				highestId = messages[i][0];
				var newMessage = messages[i][2];
				messageScreen.innerHTML += newMessage + "<br>";

				console.log(highestId);
			}
			console.log(messages);
		}, 1000);

	} else {
		alert("please submit a username")
	}
}

function sendMessage() {

	//finds the the message that has to be displayed
	var messageString = "<b>" + username + ": </b>" + document.getElementById("chat-text-area").value;

	//only submit a message if the message isn't nothing
	if (messageString != "") {
		// sends message to server
		postMessage(messageString);
	}

	//reset text input field
	document.getElementById("chat-text-area").value = "";

	//make sure the most recent messages are always shown.
	scrollToBottom("message-screen");
}

function scrollToBottom(id){
   var div = document.getElementById(id);
   div.scrollTop = div.scrollHeight - div.clientHeight;
}

function grabMessageById(id) {
	request.open("GET", "api.php?key=" + key + "&minimumid=" + id, false);
	request.send();
	return request.response;
}

function postMessage(message) {
	request.open("PUT", "api.php?&key=" + key + "&message=" + message, false);
	request.send();
}

function getUserName() {
	return document.getElementById("username-input").value;
}

function getKey() {
	return document.getElementById("chatroom-input").value;
}

function hideLoginScreen() {
	document.getElementById("login-screen").style.display = "none";	
}

function showChatroom() {
	document.getElementById("chatroom").style.display = "block";
}