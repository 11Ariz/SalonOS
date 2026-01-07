<?php
// 1. Start the session so we can access and destroy it
session_start();

// 2. Unset all session variables (user_id, user_name, role)
$_SESSION = array();

// 3. Optional but recommended: Delete the session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destroy the session on the server
session_destroy();

// 5. Redirect to the new landing page
header("Location: index.php");
exit();
?>