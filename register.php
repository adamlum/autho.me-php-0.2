<?php
	session_start();
	require_once("authome/api.php");
	$auth_sess = new Session(load_config(dirname(__FILE__) . "/config/testing.js"));
	if (count($_POST) > 0) {
		// get the registration state
		$reg_state = $_SESSION["salt"];
		session_destroy();
		// make sure they used the good salt
		if ($reg_state != $_POST["salt"]) {
			echo "Sorry, Bad Attempt";
			exit();
		}
		// all done, finish the registration
		$state = $auth_sess->register_finish($_POST["username"], $_POST["salt"], $_POST["verifier"]);
		echo "OK";
		exit();
	}
	else {
		// get the starter data for a registration
		$session_start_data = $auth_sess->register_start();
		// keep this for sanity check in step 2
		$_SESSION["salt"] = $session_start_data["salt"];
		// send it on, doesn't need to be stored
		$state = $session_start_data["state"];
	}
	include("includes/header.html");
?>
<form action="/register.php" method="POST" id="register_form">
    <fieldset>
    <legend>Registration</legend>

    <label>Username</label>
    <input id="username" value="" name="username" />

    <label>Password</label>
    <input id="password" value="" name="password" type="password" />

    <label>Password (Again)</label>
    <input id="password_two" value="" name="password_two" type="password" />

    <p class='error'></p>
    <input type="button" value="Register" class="submit" id="submit_button" onclick="register_finish();" />
    </fieldset>
</form>

<ol id="test_log">
</ol>
<p id="final_result">
</p>

<script>
    var state = <?php echo $state ?>;
    register_start(state);
</script>
<?php include("includes/footer.html"); ?>
