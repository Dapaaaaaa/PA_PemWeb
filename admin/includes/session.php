<?php 

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    function isAdminLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    function requireAdminLogin() {
        if (!isAdminLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }

    function adminLogout() {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

?>