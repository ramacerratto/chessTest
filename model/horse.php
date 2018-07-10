<?php
require_once 'piece.php';

class Horse extends Piece{
    
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
        if($xNew > 7 || $xNew < 0 || $yNew > 7 || $yNew < 0 || ($xOld == $xNew && $yOld == $yNew) ){
            return false; //Out of range or same position
        }
        
        if($xOld == -1 && $yOld == -1){ //First move
            return true;
        }
        //Valid moves for horse:
        $xSub = abs($xNew - $xOld); 
        $ySub = abs($yNew - $yOld); 
        if( ($xSub == 2 && $ySub == 1) || ($ySub == 2 && $xSub == 1) ){
            return true;
        }
        return false;
    }
    
    /**
     * Gets the best position to be nearer to the queen without
     * being reacheble.
     * The best position is calculated based in a score.
     * 
     * @param int $xTo
     * @param int $yTo
     * @return mixed array of best position on succed | false on failure
     * 
     */
    public function moveNearTo($pieces) {
        if(!$this->alive){ //If it is not alive
            return false;
        }
        $queen = $pieces['2']; //In this case we only have one black piece  
        $x = $this->xPos;
        $y = $this->yPos;

        //Getting all the posible movements of the horse:
        $moves = array();
        $checkMateMove = array();
        
        for($i=0;$i<2;$i++){
            $num1 = ($i%2 == 0)?2:1;
            $num2 = ($i%2 == 0)?1:2;
            for($j=0;$j<2;$j++){
                $sign1 = ($j%2 == 0)?-1:1;
                for($k=0;$k<2;$k++){
                    $sign2 = ($k%2 == 0)?-1:1;
                    $xNew = $x + ($sign1*$num1);
                    $yNew = $y + ($sign2*$num2);
                    $moves[] = array($xNew,$yNew);
                }
            }
        }

        //Remove the positions that make the horse reacheble to the queen:
        //And those which are out of range
        foreach ($moves as $key => $move) {
            if(!$this->validate($move[0],$move[1]) ){
                unset($moves[$key]);
            }else{
                $checkMateMove = $move;
                //Search for spaces that are reacheble to the queen:
                $queenMoves = $queen->getPosibleMoves();
                foreach ($queenMoves as $queenMove) {
                    if($move[0] == $queenMove[0] && $move[1] == $queenMove[1]){
                        unset($moves[$key]);
                    }
                }
            }
        }
        
        if(empty($moves)){ //It means checkmate
            return array($checkMateMove[0],$checkMateMove[1],90); //I send some move because it is doomed anyway
        }
        
        return $this->getBestPosition($queen,$moves);
    }
    
}
