<?php
/**
 * Class to control the request of the index page
 *
 * @author Rama
 */
class Controller {
    
    /**
     * Renders a view. If layout is false, the view is rendered
     * without the layout
     * 
     * @param string $view
     * @param boolean $layout
     */
    public function render($view,$layout = true){
        $controller = str_replace('controller','',strtolower(get_called_class()));
        $view = './views/'.$controller."/".$view.".php";
        if($layout){
            require_once('./views/layout.php');
        }else{
            require_once($view);
        }
    }
    
}
