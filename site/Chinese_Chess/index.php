<!DOCTYPE html>
<html lang="zh-tw">

<?php
!defined('APP_ROOT') && define('APP_ROOT', "../");

require_once 'check.php';
require_once APP_ROOT . 'account/check.php';

$AccountName = getAccountName();
$PlayID = getLastPlay($AccountName);
if ($PlayID != null) {
    $State = getPlayState($PlayID);

    if (explode(";", $State)[0] == "Playing") { //如果是遊戲中
        header("Location: game.php");
    }
}


?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="象棋 Chess">
    <meta name="author" content="Axisflow">
    <link type="image/png" rel="icon" href="favicon.png">
    <title>中國象棋</title>
    <script type="text/javascript" src="<?php echo APP_ROOT; ?>php.js"></script>
    <script type="text/javascript">
        function start(state = 'launch') {
            let states = state.split(";");
            let PlayID;
            let SP;
            let NSP;
            if(states.length == 4) {
                SP = states[1];
                NSP = states[2];
                PlayID = states[3];
            }

            if(states[0]=='launch') {
                php("start=true", "process.php", start);
            }
            /* let result = wait(5, 'start'); */
            else if(states[0]=='success') {
                document.getElementById("SP").innerHTML = SP;
                document.getElementById("NSP").innerHTML = NSP;
                document.getElementById("RoomID").innerHTML = PlayID;
                if(SP != '' && NSP != '') {
                    document.getElementById("start").disabled = false;
                    setTimeout(function() {
                        window.location.replace("game.php");
                    }, 1000);
                }

                setTimeout(function() { php("check=room", "process.php", start); }, 3000);
            } else {
                alert("Error! "+state);
            }
        }

        function join(state) {
            let states = state.split(";");
            let PlayID;
            if(states.length == 2) {
                PlayID = states[1];
            }

            if(states[0]=='launch') {
                php("join="+PlayID, "process.php", start);
            } else {
                alert("Error!");
            }
        }
    </script>
</head>
<body>
    <div>
        <p>Room ID<span onclick="navigator.clipboard.writeText(document.getElementsByTagName('span')['RoomID'].innerHTML);" style="cursor: pointer;">📋</span>: <span id="RoomID"></span></p>
        <p>Starting Player: <span id="SP"></span></p>
        <p>Non-Starting Player: <span id="NSP"></span></p>
        <p><button id="start" onclick="start()">Create Room</button></p>
        <p><input id="join" type="text" /><button onclick="join('launch;'+document.getElementById('join').value)">Join Game</button></p>
    </div>
</body>
</html>