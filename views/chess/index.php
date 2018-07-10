
<div class="chess row">
    <div class="table_container col-9">
        <div class="square">
            <table>
                <?php for($y=0; $y < 8; $y++):?>
                    <tr>
                        <?php for($x=0; $x < 8; $x++){
                            echo "<td id='$x$y' x='$x' y='$y'></td>";
                        } ?>
                    </tr>
                <?php endfor; ?>
            </table>
        </div>
    </div>
    <div class="col-3 center">
        <div class="pieces_box">
            <img class="piece" src="./webroot/img/horse.png" id="piece_0">
            <img class="piece" src="./webroot/img/queen.png" id="piece_2">
            <img class="piece" src="./webroot/img/horse.png" id="piece_1">
        </div>
        <div class="message">
            <p>To start the game please place each piece on the board by clicking on it and then on the board.</p>
        </div>
        <button style="display:none;" class="btn" id="btn_next">NEXT MOVE</button>
    </div>
</div>

<script>
    $(document).ready(function(){
        var url = "index.php?controller=Chess&action=makeMove";
        var turn = 'white'; //Start the white pieces
        $('#btn_next').click(function(){
            $.post(url,{turn:turn},function(response){ 
                var response = jQuery.parseJSON(response);
                if (response.checkMate){
                    $('.message > p').text('Jah! CheckMate!');
                    $('.message').fadeIn(2000);
                }
                move(response.id,response.move);
                if(response.nextTurn === 'FINISH'){
                    finish(response.winner,response.other_data);
                }else{
                    turn = response.nextTurn;
                }
            });
        });
        
        //Event of the selection of the piece:
        var selected_piece = false;
        var count = 0;
        $('.pieces_box').on('click','.piece',function(){
            selected_piece = $(this).attr('id');
            $(this).css('background-color','#CCC');
        });
        
        //Event of the selection of the board place:
        $('table').on('click','td',function(){
           if(selected_piece){
               $('#'+selected_piece).css('background-color','transparent');
               $('#'+selected_piece).detach().appendTo(this);
               var x = $(this).attr('x');
               var y = $(this).attr('y');
               var id = selected_piece.substring(6);
               sendPosition(id,x,y);
               count++;
               selected_piece = false;
               if(count == 3){
                   $('.message > p').text('Thank you, enjoy the game...');
                   $('.message').fadeOut(2000,function(){
                       $('#btn_next').fadeIn(1000);
                   });
               }
           } 
        });
        
        function sendPosition(id,x,y){
            var url = "index.php?controller=Chess&action=selectPosition";
            $.post(url,{id:id,xPos:x,yPos:y},function(response){
                console.log('ok position');
            });
        }
        
        /**
         * Moves the piece element
         * 
         * @argument {int} id
         * @argument {json} move
         */
        function move(id,move){
            if($('#'+move[0]+move[1]).children('img').length){
                $('#'+move[0]+move[1]).children('img').detach().appendTo('.pieces_box');
            }
            $('#piece_'+id).detach().appendTo('#'+move[0]+move[1]);
        }
        
        function finish(winner,other_data){
            $('.message > p').text(winner +' wins!');
            $('.message > p').append('<br> Movements: '+other_data.moves+'<br>');
            $('.message > p').append('Kills: <br>');
            for(var x in other_data.kills){
                $('.message > p').append(other_data.kills[x]+'<br>');
            }
            
            if($('#btn_next').is(":visible")){
                $('#btn_next').fadeOut(1000,function(){
                    $('.message').fadeIn(2000);
                });
            } 
        }
        
    });
</script>