<?php
/* 
    Created on : 17/07/2017, 21:18:21
    Author     : Ramiro Cerratto
*/

class Piece{
    
    public $id;
    public $xPos;
    public $yPos;
    public $alive;
    public $killedBy;
    
    public function __construct($id) {
        $this->id = $id;
        $this->xPos = -1;
        $this->yPos = -1;
        $this->alive = true;
        $this->killedBy = -1;
    }
    
    /**
     * Changes the position of the piece
     * 
     * @param int $x
     * @param int $y
     * @return boolean
     */
    public function changePosition($x,$y){
        $this->xPos = $x;
        $this->yPos = $y;
        return true;
    }
    
    /**
     * Calculate the best position from an array of posible
     * positions. 
     * The best position is the nearest one.
     * 
     * @param int $xTo
     * @param int $yTo
     * @param array $moves
     * @return boolean
     */
    public function getBestPosition($piece, $moves = array()){
        if(empty($moves)){
            return false;
        }
        $xTo = $piece->xPos;
        $yTo = $piece->yPos;
        
        //Best Position algorithm
        $bestPos = array(-1,-1,95);
        foreach ($moves as $move) {
            $subX = abs($xTo - $move[0]);
            $subY = abs($yTo - $move[1]);
            $prom = ($subX+$subY)/2;
            if($prom < $bestPos[2]){
                $bestPos[0] = $move[0];
                $bestPos[1] = $move[1];
                $bestPos[2] = $prom;
            }elseif($prom == $bestPos[2]){ 
                //Sometimes we follow our heart
                if(rand(0,10)%2 == 0){
                    $bestPos[0] = $move[0];
                    $bestPos[1] = $move[1];
                    $bestPos[2] = $prom;
                } //Well.. if it is the same really
            }
        }
        
        //If the new position is the same of the other piece, the first one killed the second one
        if($xTo == $bestPos[0] && $yTo == $bestPos[1]){
            $piece->alive = false;
            $piece->killedBy = $this->id;
            $_SESSION['other_data']['kills'][] = get_class($this)." ".$this->id." killed ".get_class($piece)." ".$piece->id;
        }
        
        return $bestPos;
    }

}
