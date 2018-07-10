<?php
/* 
    Created on : 17/07/2017, 21:18:21
    Author     : Ramiro Cerratto
*/
require_once 'piece.php';

class Queen extends Piece{
    
    public function __construct($id) {
        parent::__construct($id);
    }
    
    public function changePosition($x,$y) {
        if($this->validate($x,$y)){
            return parent::changePosition($x, $y);
        }else{
            return false;
        }
    }
    
    /**
     * Validates that the given position is valid
     * 
     * @param int $xNew
     * @param int $yNew
     * @return boolean
     */
    private function validate($xNew,$yNew){
        $xOld = $this->xPos;
        $yOld = $this->yPos;
        if($xNew > 7 || $xNew < 0 || $yNew > 7 || $yNew < 0 || ($xOld == $xNew && $yOld == $yNew)){
            return false; //Out of range or same position
        }

        return true;
    }
    
    /**
     * Gets ALL the posible moves of the queen.
     * 
     * @return array
     */
    public function getPosibleMoves(){
        $x = $this->xPos;
        $y = $this->yPos;
        
        //Getting all the posible movements of the queen:
        $moves = array();
        
        for($i=$x+1;$i<8;$i++){ //Horizontal right moves
            $xNew = $i;
            $yNew = $y;
            $moves[] = array($xNew,$yNew);
        }
        for($i=$x-1;$i>=0;$i--){ //Horizontal left moves
            $xNew = $i;
            $yNew = $y;
            $moves[] = array($xNew,$yNew);
        }
        for($i=$y+1;$i<8;$i++){ //Vertical down moves
            $xNew = $x;
            $yNew = $i;
            $moves[] = array($xNew,$yNew);
        }
        for($i=$y-1;$i>=0;$i--){ //Vertical up moves
            $xNew = $x;
            $yNew = $i;
            $moves[] = array($xNew,$yNew);
        }
        $count = 0;
        for($i=$x-1;$i>=0;$i--){ //Diagonal left moves
            $count++;
            $xNew = $i;
            $yNew = $y-$count;
            $moves[] = array($xNew,$yNew);
            $yNew2 = $y+$count;
            $moves[] = array($xNew,$yNew2);
        }
        $count = 0;
        for($i=$x+1;$i<8;$i++){ //Diagonal right moves
            $count++;
            $xNew = $i;
            $yNew = $y-$count;
            $moves[] = array($xNew,$yNew);
            $yNew2 = $y+$count;
            $moves[] = array($xNew,$yNew2);
        }
        
        //Remove the are out of range positions
        foreach ($moves as $key => $move) {
            if(!$this->validate($move[0],$move[1]) ){
                unset($moves[$key]);
            }
        }
        
        return $moves;
    }
    
    /**
     * Moves the queen near a piece.
     * It searches for the best option to move
     * 
     * @param Piece piece
     * @return array result
     */
    public function moveNearTo($pieces) {
        $moves = $this->getPosibleMoves();
        //Search for the best position to the pieces of the other side (if alive)
        $bests = array();
        foreach ($pieces as $key => $piece){
            if($piece->alive){
                $bests[] = $this->getBestPosition($piece, $moves);
            }
        }
        if(count($bests) > 1){ //Return the best option:
            return ($bests[0][2] < $bests[1][2])?$bests[0]:$bests[1];
        }else{
            return $bests[0];
        }
    }

}
