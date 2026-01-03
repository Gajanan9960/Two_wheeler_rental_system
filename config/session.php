<?php
// Secure Session Settings
if (session_status() === PHP_SESSION_NONE) {
    // Prevent JavaScript access to session cookie
    ini_set('session.cookie_httponly', 1);
    
    // Secure cookie only if HTTPS (optional, but good practice to check)
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }

    // strict mode
    ini_set('session.use_strict_mode', 1);

    session_start();
}
?>
