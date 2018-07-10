<?php
/**
 * Description of logs
 *
 * @author gyl
 */
class Logs {
    
    public static function openFile(){
        $date = date("Y-m-d");
        return fopen("logs/".$date.".log", 'a');
    }
    
    public static function write($message){
        $file = self::openFile();
        $date = date("[d/m/Y H:i:s]");
        $text = '[' . $date  . '] ' . $message . "\r\n";
        fputs($file, $text);
    }
}
