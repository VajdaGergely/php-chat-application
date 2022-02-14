function load_page(page)
{
	//hide all elements that not visible by default
	$(".pages").hide();
	$(".error-msg").remove();
	$("#save-profile-lnk").hide();
	$("#profile-page fieldset").attr("disabled", "disabled");
	
	//show choosen page and set it's elements
	switch(page)
	{
		//pages with no need of authentication
		case "welcome-page":
		case "signup-page":
		case "login-page":
			$("#menu-items").hide();
			$("#" + page).show();
			break;
		//pages with need of authentication
		case "home-page":
		case "profile-page":
		case "users-page":
		case "conversations-page":
			if(window.sessionStorage.getItem("isAuthenticated") != "true")
			{
				//not authenticated - display login page
				alert("Please login first!");
				$("#login-page").show();
			}
			else
			{
				$("#menu-items").show();
				$("#" + page).show();
			}
			break;
		default:
			alert("An error occured!");
			break;
	}
	
	//if no error occurs return true
	return true;
}

function is_login_valid(logname, pass)
{
	if(logname === "" || pass === "")
	{
		alert("Empty logname or password field!");
		return false;
	}
	else
	{
		return true;
	}
}

//ezeket a fuggvenyeket kell kipakolni kulon script file-ba!!!!
function login()
{
	var logname = $("#login-form #logname").val();
	var pass = $("#login-form #pass").val();
	if(!is_login_valid())
	{
		//add error message
		$(".error-msg").remove();
		var error_msg = "Invalid login data";
		$($("#login-form p")[1]).append("<span class=\"error-msg\" id=\"login-error-msg\">" + error_msg + "</span>");
	}
	else
	{
		var parameters = {logname : logname, pass : pass};
		$.ajax({
			type: "POST",
			url: "index.php/login",
			data: JSON.stringify(parameters),
			dataType: "text",
			contentType: "application/json",
			success: function(result){
				if(JSON.parse(result).status == "success")
				{
					window.sessionStorage.setItem("isAuthenticated", true);
					load_page("home-page");
				}
				else if(JSON.parse(result).status == "fail")
				{
					//add error message
					$(".error-msg").remove();
					var error_msg = "Wrong login name or password!";
					$($("#login-form p")[1]).append("<span class=\"error-msg\" id=\"login-error-msg\">" + error_msg + "</span>");
				}
				else
				{
					//handling server side error responses...
					//general messsage has to be shown
				}
			},
			error: function(result){
				alert("An error occured!");
				return false;
			}
		});
	}
}

function logout()
{
	$.ajax({
		type: "POST",
		url: "index.php/logout",
		success: function(result){
			//handling server json states
		},
		error: function(result){
			alert("An error occured. Logout may not be done properly!");
		}
	});
	document.cookie = "session=;Path=/index.php;Max-Age=0"; //delete cookie
	window.sessionStorage.setItem("isAuthenticated", false);
	$("form input").val(""); //delete all form data in the page
	load_page("welcome-page");
}

function is_signup_valid(logname, pass, pass2, alias, age, gender, intro)
{
	if(logname === "" || pass === "" || pass2 === "" || alias === "" || age === "" || gender === "")
	{
		alert("Fields marked with * can not be empty!");
	}
	else if(logname.length < 4 || logname.length > 10) //|| !regexp... /[a-z,A-Z,0-9,_]/G .... 
	{
		alert("Logname has to be between 4 and 20 character and must contain only letters, numbers and _ sign!")
	}
	else if(pass != pass2)
	{
		alert("Password and password2 has to be the same!");
	}
	//pass1 length min 10 max 20
	//pass2 length min 10 max 20
	//alias legnth min 4 max 20, barmilyen karakter lehet benne
	//age only number min 18 max 120
	//gender ... 0 vagy 1 semmi mas nem jo
		//ez egyebkent is majd selectbox lesz nem textbox
	//intro... nem kotelezo barmilyen karakter lehet benne
		//min 0 max 500
	
	else
	{
		return true;
	}
	return false;
}

function signup()
{
	var logname = $("#signup-form #logname").val();
	var pass = $("#signup-form #pass").val();
	var pass2 = $("#signup-form #pass2").val();
	var alias = $("#signup-form #alias").val();
	var age = $("#signup-form #age").val();
	var gender = $("#signup-form #gender").val();
	var intro = $("#signup-form #intro").val();
	
	//is_signup_valid(logname, pass, pass2, alias, age, gender, intro)
	if(!is_signup_valid(logname, pass, pass2, alias, age, gender, intro))
	{
		//add error message
		$(".error-msg").remove();
		var error_msg = "Wrong registration data!";
		$($("#signup-form p")[1]).append("<span class=\"error-msg\" id=\"signup-error-msg\">" + error_msg + "</span>");
	}
	else
	{
		var parameters = {logname : logname, pass : pass, alias : alias, age : age, gender : gender, intro : intro};
		$.ajax({
			type: "POST",
			url: "index.php/create/account",
			data: JSON.stringify(parameters),
			dataType: "text",
			contentType: "application/json",
			success: function(result){
				if(JSON.parse(result).status == "success")
				{
					alert("Signup was successfull!");
					load_page("login-page");
				}
				else
				{
					//handling server side error responses...
					//...general not-detailed error message
				}
			},
			error: function(result){
				alert("An error occured!");
				return false;
			}
		});
	}
}

function get_profile_data()
{
	$.ajax({
		type: "POST",
		url: "index.php/get/account",
		success: function(result){
			$("#account-form #alias").val(result["data"][0]["alias"]);
			$("#account-form #age").val(result["data"][0]["age"]);
			$("#account-form #gender").val(result["data"][0]["gender"]);
			$("#account-form #intro").val(result["data"][0]["intro"]);
		},
		error: function(result){
			alert("An error occured. Logout may not be done properly!");
		}
	});
}

function edit_profile()
{
	var parameters = {
		alias : $("#account-form input")[0].value, 
		age : $("#account-form input")[1].value,
		gender : $("#account-form input")[2].value,
		intro : $("#account-form input")[3].value,
	};
	$.ajax({
		type: "POST",
		url: "index.php/edit/account",
		data: JSON.stringify(parameters),
		dataType: "text",
		contentType: "application/json",
		success: function(result){
			if(JSON.parse(result).status == "success")
			{
				alert("Account updated successfully!");
				//legyen megint disabled
				$("#save-profile-lnk").hide();
				$("#profile-page fieldset").attr("disabled", "disabled");
			}
			else
			{
				alert("Error. Account have not been updated!");
			}
		},
		error: function(result){
			alert("An error occured!");
			return false;
		}
	});
}

function delete_profile()
{
	$.ajax({
		type: "POST",
		url: "index.php/delete/account",
		success: function(result){
			document.cookie = "session=;Path=/index.php;Max-Age=0";
		},
		error: function(result){
			document.cookie = "session=;Path=/index.php;Max-Age=0";
			alert("An error occured. Logout may not be done properly!");
		}
	});
	window.sessionStorage.setItem("isAuthenticated", false);
	load_page("welcome-page");
}

function get_user_details(id)
{
	$.ajax({
		type: "POST",
		url: "index.php/get/user",
		data: '{"id":"' + id + '"}',
		dataType: "text",
		contentType: "application/json",
		success: function(result){
			var obj = JSON.parse(result);
			if(obj.status == "success")
			{
				//display
				$("#user-details p").empty();
				var content = "<table>";
				content += "<tr><td>Alias:</td><td>" + obj.data[0].alias + "</td></tr>";
				content += "<tr><td>Age:</td><td>" + obj.data[0].age + "</td></tr>";
				content += "<tr><td>Gender:</td><td>" + obj.data[0].gender + "</td></tr>";
				content += "<tr><td>Intro:</td><td>" + obj.data[0].intro + "</td></tr>";
				content += "</table>";
				$("#user-details p").append(content);
			}
			else
			{
				//error handling code
				//...
			}
		},
		error: function(result){
			alert("An error occured. Logout may not be done properly!");
		}
	});
}

function get_user_list()
{
	$.ajax({
		type: "POST",
		url: "index.php/get/user/list",
		success: function(result){
			var obj = result;
			if(obj.status == "success")
			{
				$("#user-list p ul").empty();
				for(var i = 0; i < result["data"].length; i++)
				{
					$("#user-list ul").append("<li><a class=\"user-list-lnk\" id=\"" + obj.data[i].id + "\" href=\"#\">" + obj.data[i].alias + "</a></li>");
				}
				$(".user-list-lnk").click(function(){
					get_user_details($(this).attr("id"));
				});
			}
			else
			{
				//error handling code
				//...
			}
		},
		error: function(result){
			alert("An error occured. Logout may not be done properly!");
		}
	});
}

function get_conversation(id)
{
	//ajax call
	$.ajax({
		type: "POST",
		url: "index.php/get/message",
		success: function(result){
			/*var messages = [
				{
					time : "2021.11.08 11:34:24",
					sender : "phil32",
					text : "Hi dude what's up?"
				},
				{
					time : "2021.11.08 11:35:12",
					sender : "alisha98",
					text : "All good mate."
				}
			];
			
			//display
			$("#messages-panel").empty();
			//kesobb ez textarea lesz majd!!!
			var content = "<table><tr><th>time</th><th>sender</th><th>text</th></tr>";
			for(var i in messages)
			{
				content += "<tr>";
				content += "<td>" + messages[i].time + "</td>";
				content += "<td>" + messages[i].sender + "</td>";
				content += "<td>" + messages[i].text + "</td>";
				content += "</tr>";
			}
			content += "</table>";
			$("#messages-panel").append(content);*/
		},
		error: function(result){
			alert("An error occured. Logout may not be done properly!");
		}
	});
}

function get_conversation_list()
{
	//ajax 
	$.ajax({
		type: "POST",
		url: "index.php/get/message/list",
		success: function(result){
			obj = JSON.parse(result);
			$("#conversation-list ul").empty();
			$("#conversation-list ul").append("<li><a class=\"conversation-list-lnk\" id=\"" + obj.data[0].id + "\" href=\"#\">" + obj.data[0].alias + "</a></li>")
			$("#conversation-list ul").append("<li><a class=\"conversation-list-lnk\" id=\"44444444\" href=\"#\">evelin</a></li>")
			$(".conversation-list-lnk").click(function(){
				get_conversation($(this).attr("id"));
			});
		},
		error: function(result){
			alert("An error occured. Logout may not be done properly!");
		}
	});
}

function send_new_msg()
{
	//ajax 
	$.ajax({
		type: "POST",
		url: "index.php/create/message",
		data: '{"receiver_id":"' + $("#new-message-receiver_id").val() + '","text":"' + $("#new-message-text").val() + '"}',
		dataType: "text",
		contentType: "application/json",
		success: function(result){
			//fuzze hozza a sajat uzenetet a listahoz kiirva
			alert("good");
		},
		error: function(result){
			alert("An error occured. Message has not sent!");
		}
	});
}
