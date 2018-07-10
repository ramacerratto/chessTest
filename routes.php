<?php

function call($controller, $action) {
    
    //Require the file that matches the controller name
    require_once('controller/' . ucfirst($controller) . 'Controller.php');

    //Create a new instance of the needed controller
    switch ($controller) {
        case 'pages':
            $controller = new PagesController();
            break;
        case 'chess':
            $controller = new ChessController();
            break;
    }

    //Call the action of the controller
    $controller->{ $action }();
}

//We consider this "allowed" values
$controllers = array(
    'pages' => ['home'],
    'chess' => ['home','index','makeMove','selectPosition']
);

//Check that the requested controller and action are both allowed
//If someone tries to access something else he will be redirected to the error action of the pages controller
if (array_key_exists($controller, $controllers)) {
    if (in_array($action, $controllers[$controller])) {
        call($controller, $action);
    } else {
        call('pages', 'error');
    }
} else {
    call('pages', 'error');
}
?>