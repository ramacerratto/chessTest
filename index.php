<?php
/*
 *  Author: Rama Cerratto
 */
if (isset($_GET['controller']) && isset($_GET['action'])) {
    $controller = strtolower($_GET['controller']);
    $action = $_GET['action'];
} else {
    $controller = 'pages';
    $action = 'home';
}

require_once('routes.php');
?>