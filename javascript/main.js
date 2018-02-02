loginUser();

// Navigates to a specific page
function navigateTo(contentToDisplay) {

	if (currentContent != contentToDisplay) {

		if (contentToDisplay == 'blog-content') {

			blogContent = getAllBlogContentFromAPI();
			displayBlogContent(blogContent);

			categoryContent = getAllCategoriesFromAPI();
			displayCategories(categoryContent);
			
		}

		if (contentToDisplay == "add-category") {

			refreshCategoriesForBlogger();

		}

		if (contentToDisplay == "create-article") {

			categories = getAllCategoriesFromAPI();

			// Creates drop down menu of all the categories for blogger
			document.getElementById("categories-message").innerHTML = "<option value=" + categories[0][1] + ">" + categories[0][1] + "</option>";
			
			for (i = 1 ; i < categories.length ; i++) {

				document.getElementById("categories-message").innerHTML += "<option value=" + categories[i][1] + ">" + categories[i][1] + "</option>";

			}

		}

		if (currentContent != "") {

			// Hides the content it is currently showing
			$("#" + currentContent).toggle('slow');

		}

		// Shows the content you are navigating to
		$("#" + contentToDisplay).toggle('slow');

		currentContent = contentToDisplay;

	}

}

// Navigates the page to the user interface
function loginUser() {

	document.getElementById('user-interface').style.display = "block";
	navigateTo('home-content');

}
// Checks blogger credentials and logs in the blogger if the API returns true
function loginBlogger() {

	username = document.getElementById("username").value;
	password = document.getElementById("password").value;

	if (checkLogin(username, password)) {

		document.getElementById('user-interface').style.display = "none";
		document.getElementById('add-category').style.display = "none";
		document.getElementById('blogger-interface').style.display = "block";
		navigateTo('create-article');

	} else {

		alert("Please provide valid login credentials");

	}
	
}

// Displays blogposts depending on the category provided
function displayContentByCategory(category) {

	$("#content").toggle('fast');

	request.open("GET", "api.php?category=" + category, false);
	request.send();

	response = JSON.parse(request.response);

	displayBlogContent(response);

	$("#content").toggle('fast');

}