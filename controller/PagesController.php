<?php
require('Controller.php');
/**
 * ChessController
 *
 * @author Rama
 */
class PagesController extends Controller {
    
    public function home(){
        $this->render('home');
    }
    
    public function error($message = null){
        $this->render('error');
    }
    
}
