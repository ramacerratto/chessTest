<?php
require_once('Controller.php');
require_once('utility/Logs.php');
/**
 * ChessController
 *
 * @author Rama Cerratto
 */
class ChessController extends Controller {
    
    public function __construct() {
        session_start(); 
        require_once 'model/horse.php';
        require_once 'model/queen.php';
    }
    
    /**
     * Initialize game
     */
    public function index(){
        Logs::write("New game");
        $horse1 = new Horse(0);
        $horse2 = new Horse(1);
        $queen = new Queen(2);
        
        //Store the actual game states in session:
        $_SESSION['pieces'] = serialize([
            'white' => [
                '0' => $horse1,
                '1' => $horse2
            ],
            'black' => ['2' => $queen]
        ]);
        $_SESSION['other_data'] = [
            'moves' => 0,
        ];
        
        $this->render('index');
    }
    
    /**
     * Save the initial positions of the pieces
     * 
     */
    public function selectPosition() {
        $xPos = $_POST['xPos'];
        $yPos = $_POST['yPos'];
        $pieceId = $_POST['id'];
        
        $sides = unserialize($_SESSION['pieces']);
        foreach($sides as $pieces){
            if(isset($pieces[$pieceId])){
                $pieces[$pieceId]->changePosition($xPos,$yPos);
                Logs::write("Position Selected: ".get_class($pieces[$pieceId])." $pieceId in ($xPos,$yPos)");
                break;
            }
        }
        $_SESSION['pieces'] = serialize($sides);
        echo 'OK';
    }
    
    /**
     * Make a move
     * 
     */
    public function makeMove(){
        $sides = unserialize($_SESSION['pieces']);
        $bestMove = [
            'move' => array(-1,-1,100),
            'idPiece' => 0,
            'checkMate' => false
        ];
        
        //I receive the turn (black or white)
        $turn = $_POST['turn'];
        $pieces = $sides[$turn];
        $nextTurn = ($turn == 'black')?'white':'black';
        $otherPieces = $sides[$nextTurn];
        
        foreach($pieces as $piece){
            $move = $piece->moveNearTo($otherPieces);
            if($move != false && $move[2] < $bestMove['move'][2]){
                $bestMove['move'] = $move;
                $bestMove['idPiece'] = $piece->id;
                $bestMove['name'] = get_class($piece);
                if($move[2] == 90){ //Cod. for checkmate
                    $bestMove['checkMate'] = true;
                }else{
                    $bestMove['checkMate'] = false;
                }
            }
        }
        
        $log = ucfirst($turn)." move: ".$bestMove['name']." ".$bestMove['idPiece']." to ({$bestMove['move'][0]},{$bestMove['move'][1]})";
        $log .= ($bestMove['checkMate'])?" [CheckMate]":"";
        Logs::write( $log );
        //Check if game is finished checking if the other side pieces are dead
        $finish = true;
        foreach ($otherPieces as $piece) {
            if($piece->alive){
                $finish = false;
            }
        }
        if($finish){ 
            $nextTurn = 'FINISH';
            $winner = $turn;
            Logs::write(ucfirst($turn)." wins.");
        }
        
        //Register the move:
        $sides[$turn][$bestMove['idPiece']]->changePosition($bestMove['move'][0],$bestMove['move'][1]);
        
        //Save the data
        $_SESSION['pieces'] = serialize($sides);
        $_SESSION['other_data']['moves']++;
        
        //And respond with the movement and the id of the piece to move
        $return = array(
            'move' => $bestMove['move'],
            'id' => $bestMove['idPiece'],
            'nextTurn' => $nextTurn,
            'checkMate' => $bestMove['checkMate']
        );
        if($nextTurn == 'FINISH'){ //If is finished i add the winner and extra data
            $return['winner'] = $winner;
            $return['other_data'] = $_SESSION['other_data'];
        }
        echo json_encode($return);
        
        /*
        if($turn == 'black'){
            $move = $pieces[2]->moveNearTo($pieces); //Search for best moves of the queen
            $idPiece = $pieces[2]->id;
            Logs::write("Black move: ".get_class($pieces[2])." ".$pieces[2]->id." to ({$move[0]},{$move[1]})");
            //Check if game is finished checking if the horses are both dead
            if(!$pieces[2]->alive && !$pieces[1]->alive){ 
                $nextTurn = 'FINISH';
                $winner = 'black';
                Logs::write("Black wins");
            }else{
                $nextTurn = 'white';
            }
        }else{ //Search for the best moves of one of the horses:
            $move1 = $pieces[0]->moveNearTo($pieces[2]);
            $move2 = $pieces[1]->moveNearTo($pieces[2]);
            if($move1[2] < $move2[2]){
                $move = $move1;
                $idPiece = $pieces[0]->id;
            }elseif($move1[2] > $move2[2]){
                $move = $move2;
                $idPiece = $pieces[1]->id;
            }
            Logs::write("White move: ".get_class($pieces[$idPiece])." ".$idPiece." to ({$move[0]},{$move[1]})");
            //Check if game is finished checking if the queen is dead:
            if(!$pieces[2]->alive){ 
                $nextTurn = 'FINISH';
                $winner = 'white';
                Logs::write("White wins");
            }else{
                $nextTurn = 'black';
            }
        }*/
        
    }
    
}
