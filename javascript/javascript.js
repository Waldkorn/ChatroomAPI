var username = "";
var key = "ewout";
var otherkey = "raymond";
var request = new XMLHttpRequest();
var highestId = 0;
var myKeys=[];
var yourKeys=[];
var bothKeys=[];

var messageScreen = document.getElementById("message-screen");

function login() {
	key = getUserName();
	otherkey = getKey();
	getBothMessageIds(key, otherkey);

	if (key != "") {

		hideLoginScreen();
		showChatroom();

		window.setInterval(function(){
			correctids = getBothMessageIds(key, otherkey);
			//console.log(correctids);
			refreshChat(correctids);
		}, 1000);

	} else {
		alert("please submit a username")
	}
}

function sendMessage() {

	//finds the the message that has to be displayed
	var messageString = document.getElementById("chat-text-area").value;

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

function refreshChat(correctids) {
	for (i = 0; i < correctids.length; i++) {
		if (correctids[i] >= highestId) {
			var messageId = correctids[i];
			var myKeys = getAllMessageIds(key);
			var yourKeys = getAllMessageIds(otherkey);
			//console.log(myKeys);
			//console.log(messageId);
			if (myKeys.includes(messageId)) {
				console.log(grabMessageById(key, messageId));
				var newMessage = JSON.parse(grabMessageById(key, messageId)).message;
				messageScreen.innerHTML += "<div class=myMessage><b>" + key + ": </b><br>" + newMessage + "</div><br>";
				//console.log("ewout");
			} else if (yourKeys.includes(messageId)) {
				var newMessage = JSON.parse(grabMessageById(otherkey, messageId)).message;
				messageScreen.innerHTML += "<div class=yourMessage><b>" + otherkey + ": </b><br>" + newMessage + "</div><br>";
				console.log("raymond");
			} else {
				console.log("didn't work")
			}

			

			scrollToBottom("message-screen");
			highestId = messageId + 1;
		}
	}
}

function getAllMessageIds(input) {
	//vraagt de ids aan die bij de chatroom horen
	request.open("GET" , "api.php?mykey=" + input, false);
	request.send();
	correctids = request.response;

	//correctids word van stringformaat overgezet naar array formaat met integers
	correctids = correctids.split(",");
	for (i = 0 ; i < correctids.length; i++) {
		correctids[i] = parseInt(correctids[i]);
	}
	return correctids;
}

function getBothMessageIds(input, otherkey) {
	var myKeys = getAllMessageIds(input);
	var yourKeys = getAllMessageIds(otherkey);
	//console.log(yourKeys);
	//console.log(yourKeys.includes(2979));
	var bothKeys = myKeys + "," + yourKeys;
	bothKeys = bothKeys.split(",");
	for (i = 0 ; i < bothKeys.length; i++) {
		bothKeys[i] = parseInt(bothKeys[i]);
	}
	return bothKeys.sort();
}

function scrollToBottom(id){
   var div = document.getElementById(id);
   div.scrollTop = div.scrollHeight - div.clientHeight;
}

function grabMessageById(input ,id) {
	request.open("GET", "api.php?mykey=" + input + "&lastid=" + id, false);
	request.send();
	return request.response;
}

function postMessage(message) {
	request.open("PUT", "api.php?&mykey=" + key + "&value=" + message, false);
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

function destroyEverything() {
	document.getElementById("login-screen").style.display = "none";
	document.getElementById("chatroom").style.display = "none";
	document.getElementById("fail").style.display = "block";
}