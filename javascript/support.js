var categoriesToAdd = [];
var currentContent = "";
var request = new XMLHttpRequest();

// Posts new category to the server
function createCategory() {

	newCategory = document.getElementById('new-category').value;

	request.open("POST", "api.php?category=" + newCategory, false);
	request.send();

	newCategory = "";

	refreshCategoriesForBlogger();

}

// Takes an array with categories and displays them
function displayCategories(categories) {

	document.getElementById('categories').innerHTML = "";
	for (i = 0 ; i < categories.length ; i++) {
		document.getElementById('categories').innerHTML += 
		"<div class=category-element onclick=displayContentByCategory('" + categories[i][1] + "')>" + categories[i][1] + "</div>";
	}

}

// Returns true if Login credentials match
function checkLogin(username, password) {

	request.open("GET", "api.php?username=" + username + "&password=" + password, false);
	request.send();

	if (request.response === "1") {

		return true;

	} else {

		return false;

	}
	
}

//post new message to the server
function submitMessage() {

	categories = categoriesToAdd;
	message = tinymce.get('write-message').getContent({format: 'raw'});

	request.open("POST", "api.php?categories=" + categories + "&message=" + message, false);
	request.send();

	console.log(request.response);

}

// Stores the categories that the blogger selected so it can add it to the message.
function addCategoryToMessage() {

	category = document.getElementById("categories-message").value;

	// Adds the selected category to a list of categories that have been stored and will be forwarded along with the message
	categoriesToAdd.push(category);

	categories = getAllCategoriesFromAPI();

	redactedCategories = [];

	// Removes categories from the drop-down menu so the blogger can't add the same category twice, also check if category isn't nothing
	for (i = 0 ; i < categories.length ; i++) {

		if (!categoriesToAdd.includes(categories[i][1]) && categories[i][1]!= "undefined") {

			redactedCategories.push(categories[i][1])

		}

	}

	// Display leftover categories in the drop down menu
	document.getElementById("categories-message").innerHTML = "<option value=" + redactedCategories[0] + ">" + redactedCategories[0] + "</option>";
	
	for (i = 1 ; i < redactedCategories.length ; i++) {

		document.getElementById("categories-message").innerHTML += "<option value=" + redactedCategories[i] + ">" + redactedCategories[i] + "</option>";

	}

}

// function that displays blog posts and adds the appropriate category/categories to it.
function displayBlogContent(content) {

	content.sort(sortFunction);
	highestid = Infinity;
	document.getElementById('content').innerHTML = "";

	for (i = content.length - 1 ; i > 0 ; i--) {

		if (content[i][0] < highestid) {

			document.getElementById('content').innerHTML += "<div class='blogpost' id=blogpost" + content[i][0] + "></div>";
			document.getElementById('blogpost' + content[i][0]).innerHTML = "<div class='blogpostcategory' id='blogpostcategory" + content[i][0] + "'>" + content[i][1] + "</div>";
			document.getElementById('blogpost' + content[i][0]).innerHTML += "<div class='blogpost-message'>" + content[i][2] + "</div>";
			highestid = content[i][0];

		} else if (content[i][0] == highestid) {

			document.getElementById("blogpostcategory" + content[i][0]).innerHTML += ", " + content[i][1];

		}

	}

}

// Returns an array with all blog posts
function getAllBlogContentFromAPI() {

	request.open("GET", "api.php", false);
	request.send();

	return JSON.parse(request.response);

}

// Returns an array with all categories
function getAllCategoriesFromAPI() {

	request.open("get", "api.php?getcategories=yes", false);
	request.send();

	return JSON.parse(request.response);

}

// Supporting function which helps sort the array of messages
function sortFunction(a, b) {
    if (parseInt(a[0]) === parseInt(b[0])) {
        return 0;
    }
    else {
        return (parseInt(a[0]) < parseInt(b[0])) ? -1 : 1;
    }
}

// Grabs all message from the API and displays them in a table, also adds an "add new category" button
function refreshCategoriesForBlogger() {

	categoriesObject = getAllCategoriesFromAPI();
	categories = [];

	for (i = 0 ; i < categoriesObject.length ; i++) {

		categories[i] = categoriesObject[i][1];

	}

	displayCategories(categories);

	contentWindow = document.getElementById('add-category');
	contentWindow.innerHTML = "<table id='table'></table>";

	for (i = 0 ; i < categories.length ; i++) {

		tr = document.createElement("tr");
		td = document.createElement("td");
		txt = document.createTextNode(categories[i]);

		td.appendChild(txt);
		tr.appendChild(td);
		document.getElementById('table').appendChild(tr);

	}

	contentWindow.innerHTML += "<input id='new-category' placeholder='Add new category...'>";
	contentWindow.innerHTML += "<button onclick='createCategory()'>Add Category</button>";

}