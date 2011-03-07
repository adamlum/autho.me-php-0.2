<?php
	session_start();
	require_once("authome/api.php");
	$auth_sess = new Session(load_config(dirname(__FILE__) . "/config/testing.js"));
	if (count($_POST) > 0) {
		if ($_GET["s"] == "start") {
			// first we get a post for getting their auth info
			$session_data = $auth_sess->auth_start($_POST["username"]);
			// keep the session_id we got and the username they claimed for later
			$_SESSION["session_id"]	= $session_data["session_id"];
			$_SESSION["username"] = $_POST["username"];
			// send the data as JSON
			header('Content-type: application/json');
			echo $session_data["data"];
			exit();
		}
		elseif ($_GET["s"] == "finish") {
			$auth_state_username = $_SESSION["username"];
			$auth_state_session_id = $_SESSION["session_id"];
			session_destroy(); // don't need this anymore
			// make sure the username's right
			if ($_POST["username"] != $auth_state_username) {
				echo "Bad username.";
				exit();	
			}
			// ask autho.me to verify the client proof
			$data = $auth_sess->auth_finish($auth_state_session_id, $auth_state_username, $_POST["client_proof"], $_POST["client_pub"]);
			header('Content-type: application/json');
			// send back the response, the browser also verifies the server via the proof that autho.me gave
			echo $data;
			exit();
		}
		else {
			echo "Bad Request";
			exit();
		}
	}
	include("includes/header.html"); 
?>
<form action="/auth.php" method="POST" id="auth_form">
    <fieldset>
        <legend>Login</legend>

        <label>Username</label>
        <input id="username" value="" name="username" />

        <label>Password</label>
        <input id="password" value="" name="password" type="password" />

        <p class='error'></p>
        <input id="login_button" type="button" value="Login" class="submit" onclick="auth();" />
    </fieldset>

    <ol id="test_log">
    </ol>

    <p id='final_result'></p>
</form>
<?php include("includes/footer.html"); ?>
