function log(msg)
{
    $('#test_log').append('<li class="log_msg">' + msg + '</li>');
}

function log_err(msg)
{
    $('#test_log').append('<li class="error_msg">' + msg + '</li>');
}

var AuthEvents = {
    onCryptoError: function(step, msg) {
        log_err(msg);
    },

    onNetError: function(msg) {
        log_err(msg);
    },

    onPasswordError: function() {
        log_err("Server can't verify your password."); 
    },

    onStateError: function() {
        log_err("Catastrophic error in the crypto. Good job! Tell Zed.");
    },

    onRngError: function() {
        log_err("Failed to initialize randomness.");
    },

    onServerUnverified: function() {
        log_err("Your client can't verify the server. FAIL.");
    },

    urls: {
        start: 'auth.php?s=start',
        finish: 'auth.php?s=finish'
    },

    onChallengeSent: function() {
        log("Sending challenge to server.");
    },

    onChallengeAccepted: function() {
        log("Server responded, setting up the crypto.");
    },

    onRngReady: function() {
        log("Randomness works, <b>calculating keys</b>.");
    },

    onCryptoStep: function(step, msg) {
        log(msg);
    },

    onStart: function() { 
        $('#login_button').attr('disabled', 'disabled');
        log("Starting.");
    },

    onResponseSent: function() { 
        log("Response sent."); 
    },

    onPasswordAccepted: function() {
        log("Server accepted password, verifying server.");
    },
    
    onServerVerified: function() {
        log("Server verified message.  ALL GOOD.");
    },

    onFinished: function() {
        $('#final_result').html('<h1>It Worked!</h1>' +
            '<p class="big_link"><a href="index.php">Try it again.</a></p>'
        );
    },

    onFailure: function() {
        $('#final_result').html('<h1 class="error_msg">Sorry, Fail</h1>' +
            '<p class="big_link"><a href="auth.php">Try again.</a></p>'
        );
    },

    xhr: function(url, success, failure, params) {
        new Xhr(url,
            {onSuccess: success, onFailure: failure}
            ).send(params);
    }
}

var RegisterEvents = {
    onCryptoError: function(step, msg) {
        log_err(msg);
    },

    onNetError: function() {
        log_err('Failed to register with autho.me.');
    },

    onRngError: function() {
        log_err("Failed to initialize randomness.");
    },

    onStateError: function() {
        log_err("Catastrophic error in the crypto. Good job! Tell Zed.");
    },

    onVerifierError: function(msg) {
        log_err(msg);
    },

    urls: {
        finish: 'register.php'
    },


    xhr: function(url, success, failure, params) {
        new Xhr(url,
            {onSuccess: success, onFailure: failure}
            ).send(params);
    },

    onStart: function() {
        $('#submit_button').attr('disabled', 'disabled');
    },

    onRngReady: function() {
        $('#submit_button').attr('disabled', '');
    },

    onVerifierMade: function() {
        log("Created verifier from your password.");
    },

    onFailure: function() {
        $('#final_result').html('<h1 class="error_msg">Sorry, Fail</h1>' +
            '<p class="big_link"><a href="/register">Try again.</a></p>'
        );
    },

    onFinished: function() {
        $('#final_result').html('<h1 class="error_msg">Registered!</h1>' +
            '<p class="big_link"><a href="auth.php">Test out your new login.</a></p>'
        );
    }
}

function register_start(state)
{
    Autho.register_start(state, RegisterEvents);
}

function register_finish()
{
    var username = $('#username').val();
    var password = $('#password').val();
    var password_two = $('#password_two').val();

    if(password != password_two) {
        log_err("Your passwords don't match. Try again.");
    } else {
        Autho.register_finish(username, password);
    }
}
       
function auth()
{
    var username = $('#username').val();
    var password = $('#password').val();

    Autho.auth_start(AuthEvents, 150);
    Autho.auth_finish(username, password);
}

