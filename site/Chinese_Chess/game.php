<!DOCTYPE html>
<html lang="zh-tw" style="width: 100%; height: 100%; font-size: 1vmin;">

<?php
    !defined('APP_ROOT') && define('APP_ROOT', "../");

    require_once 'check.php';
    require_once APP_ROOT.'account/check.php';

    $AccountName = getAccountName();
    $PlayID = getLastPlay($AccountName);
    if($PlayID != null) {
        $State = getPlayState($PlayID);

        if(explode(";", $State)[0] == "End"){
            header("Location: index.php");
        } else if (explode(";", $State)[0] == "PRE-1-1"){
            setPlayState($PlayID, "Playing");
        } else if (explode(";", $State)[0] == "Playing") {

        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>中國象棋</title>
    <link rel="stylesheet" href="../fonts/main.css" />
</head>

<body style="margin: 0; width: 100%; height: 100%; background-image: url('bg.jpg'); background-size: cover; background-position: center; display: flex; flex-direction: column; align-items: center; justify-content: space-between;">
    <header style="height: 2.5%;"></header>
    <main style="height:95%; width: 95%; display: flex; flex-direction: row; align-items: center; justify-content:space-around;">
        <style type="text/css">
            p {
                margin: 0;
            }
            .aside-board {
                background-image: url('texture.svg');
                background-size: cover;
                background-position: center;
                border-radius: 10px;
                border: 2.5px solid rgb(153, 76, 0);
                box-shadow: 0px 0px 10px rgb(0, 0, 0);
            }

            .info-board {
                height: 17.5%;
                width: 100%;
                font-size: 2.5em;
                text-shadow: 0px 0px 1px rgb(127, 127, 127);
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content:space-evenly;
            }
                .info-board .info-image {
                    backdrop-filter: blur(2.5px);
                    background-image: url('user.svg');
                    background-size: contain;
                    background-position: center;

                    width:27.5%;
                    padding-bottom: 27.5%;
                }
                .info-board .info-name {
                    font-family: Huninn;
                    font-size: 1.5em;
                }
                .info-board .info-time {
                    height:40%;
                    width:100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: space-evenly;

                    font-family: 'I-Ngaan';
                }
            
            .eaten-board {
                height: 77.5%;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: space-evenly;

                font-size: 3em;
                font-family: 'I-Ngaan';
                text-shadow: 0px 0px 1px rgb(127, 127, 127);
            }
                .eaten-board .eaten-column {
                    height: 100%;
                    width: 30%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: space-evenly;
                }
                .eaten-board .eaten-column span {
                    width: 100%;
                    padding-bottom: 100%;
                    background-size: cover;
                }
        </style>
        <article style="height:100%; width: 20%; display: flex; flex-direction: column; align-items: center; justify-content: space-evenly;">
            <aside id="info-opponent" class="info-board aside-board">
                <div class="info-image"></div>
                <div style="height:100%; width:67.5%; display: flex; flex-direction: column; align-items: center; justify-content: space-evenly;">
                    <label class="info-name" style="color: red;">對方：</label>
                    <div class="info-time">
                        <p><label>局時：</label><label class="info-totaltime"></label></p>
                        <p><label>步時：</label><label class="info-steptime"></label></p>
                    </div>
                </div>
            </aside>
            <aside id="eaten-opponent" class="eaten-board aside-board">
                <label>吃子</label>
                <div style="height: 90%; width: 100%; display: flex; flex-direction: row-reverse; align-items: center; justify-content: space-evenly;"> 
                    <div class="eaten-column">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <div class="eaten-column">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <div class="eaten-column">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </div>
            </aside>
        </article>
        <article id="board-container" style="height:100%; width: 50%; display: flex; align-items: center; justify-content:center;">
            <div id="chessboard" style="width: 515.4px; height: 567.1px; background-image: url('texture.png'); background-repeat: repeat; background-position: center; border-style: groove; border-width: 5px;">
                <style>
                    .board-block {
                        width: calc(9/10*100%);
                        height: calc(5/11*100%);
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: space-around;
                    }

                    .board-row {
                        width: 100%;
                        height: 20%;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        justify-content: space-around;
                    }

                        .board-row span {
                            width: calc(1/9*100%);
                            height: calc(100%);
                            background-size: cover;
                            border-color: rgb(0, 255, 255);
                            border-radius: 10px;
                            cursor: pointer;
                        }

                            .board-row span:hover {
                                background-color: rgba(0,127,255,0.5);
                            }

                    .piece {
                    }

                    .piece-null {
                    }

                    .piece-red-rook {
                        background-image: url('res/俥.png');
                        transform: rotate(180deg);
                    }

                    .piece-red-knight {
                        background-image: url('res/傌.png');
                        transform: rotate(180deg);
                    }

                    .piece-red-minister {
                        background-image: url('res/相.png');
                        transform: rotate(180deg);
                    }

                    .piece-red-guard {
                        background-image: url('res/仕.png');
                        transform: rotate(180deg);
                    }

                    .piece-red-commander {
                        background-image: url('res/帥.png');
                        transform: rotate(180deg);
                    }

                    .piece-red-cannon {
                        background-image: url('res/炮.png');
                        transform: rotate(180deg);
                    }

                    .piece-red-pawn {
                        background-image: url('res/兵.png');
                        transform: rotate(180deg);
                    }

                    .piece-black-rook {
                        background-image: url('res/車.png');
                    }

                    .piece-black-knight {
                        background-image: url('res/馬.png');
                    }

                    .piece-black-minister {
                        background-image: url('res/象.png');
                    }

                    .piece-black-guard {
                        background-image: url('res/士.png');
                    }

                    .piece-black-commander {
                        background-image: url('res/將.png');
                    }

                    .piece-black-cannon {
                        background-image: url('res/包.png');
                    }

                    .piece-black-pawn {
                        background-image: url('res/卒.png');
                    }
                </style>

                <div style="width: 100%; height: 100%; background-image: url('board.png'); background-size: cover; display: flex; flex-direction: column; align-items: center; justify-content:center;">
                    <div class="board-block">
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                    </div>
                    <div class="board-block">
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="board-row">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                    </div>
                </div>

                <script type="text/javascript">
                    (function labelIDNum() {
                                let tmp = document.getElementById('chessboard').getElementsByTagName('span');
                                for (let i = 0; i < 90; i++) {
                                    tmp[i].id = i % 9 + '-' + Math.floor(i / 9);
                                    tmp[i].onclick = function (e) { choose(tmp[i].id[0], tmp[i].id[2]); };
                                }
                    })();
                </script>
            </div>
        </article>
        <article style="height:100%; width: 20%; display: flex; flex-direction: column; align-items: center; justify-content: space-evenly;">
            <aside id="eaten-self" class="eaten-board aside-board">
                <div style="height: 90%; width: 100%; display: flex; flex-direction: row; align-items: center; justify-content: space-evenly;"> 
                    <div class="eaten-column">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <div class="eaten-column">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <div class="eaten-column">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </div>
                <label>吃子</label>
            </aside>
            <aside id="info-self" class="info-board aside-board">
                <div style="height:100%; width:67.5%; display: flex; flex-direction: column; align-items: center; justify-content: space-evenly;">
                    <label class="info-name"  style="color: blue;">我方：</label>
                    <div class="info-time">
                        <p><label>局時：</label><label class="info-totaltime"></label></p>
                        <p><label>步時：</label><label class="info-steptime"></label></p>
                    </div>
                </div>
                <div class="info-image"></div>
            </aside>
        </article>
    </main>
    <footer style="height: 2.5%;"></footer>
    <script type="text/javascript" src="<?php echo APP_ROOT; ?>php.js"></script>
    <script type="text/javascript">
        let myRealm = "<?php echo checkOrder($AccountName, $PlayID); ?>";
        let SP_Name = "<?php echo getDisplayName(getSP_UUID($PlayID)); ?>";
        let SP_Image = "<?php echo getDisplayImage(getSP_UUID($PlayID)); ?>";
        let NSP_Name = "<?php echo getDisplayName(getNSP_UUID($PlayID)); ?>";
        let NSP_Image = "<?php echo getDisplayImage(getNSP_UUID($PlayID)); ?>";
        (function setInfo() {
            let myinfo = null;
            let anotherinfo = null;
            if(myRealm == 'SP') {
                myinfo = "self";
                anotherinfo = "opponent";
            } else if(myRealm == 'NSP') {
                document.getElementById("chessboard").style.transform = "rotate(180deg)";
                myinfo = "opponent";
                anotherinfo = "self";
            }

            document.getElementById('info-' + myinfo).getElementsByClassName('info-name')[0].innerHTML = SP_Name;
            document.getElementById('info-' + anotherinfo).getElementsByClassName('info-name')[0].innerHTML = NSP_Name;
            if(SP_Image!="") document.getElementById('info-' + myinfo).getElementsByClassName('info-image')[0].style.backgroundImage = "url('" + SP_Image + "')";
            if(NSP_Image!="") document.getElementById('info-' + anotherinfo).getElementsByClassName('info-image')[0].style.backgroundImage = "url('" + NSP_Image + "')";
        })();

        function updateBoardState(step='') {
            let chessboard = [["俥", "傌", "相", "仕", "帥", "仕", "相", "傌", "俥"], ["　", "　", "　", "　", "　", "　", "　", "　", "　"], ["　", "炮", "　", "　", "　", "　", "　", "炮", "　"], ["兵", "　", "兵", "　", "兵", "　", "兵", "　", "兵"], ["　", "　", "　", "　", "　", "　", "　", "　", "　"], ["　", "　", "　", "　", "　", "　", "　", "　", "　"], ["卒", "　", "卒", "　", "卒", "　", "卒", "　", "卒"], ["　", "包", "　", "　", "　", "　", "　", "包", "　"], ["　", "　", "　", "　", "　", "　", "　", "　", "　"], ["車", "馬", "象", "士", "將", "士", "象", "馬", "車"]];
            for (let i = 0; i < step.length; i += 4) {
                chessboard[parseInt(step[i + 3])][parseInt(step[i + 2])] = chessboard[parseInt(step[i + 1])][parseInt(step[i])];
                chessboard[parseInt(step[i + 1])][parseInt(step[i])] = "　";
            }
            
            let tmp = document.getElementById('chessboard').getElementsByTagName('span');
            for (let i = 0; i < 10; i++) {
                for (let j = 0; j < 9; j++) {
                    tmp[i * 9 + j].className = "";
                    switch (chessboard[i][j]) {
                        case '俥':
                            tmp[i * 9 + j].classList.add('piece-red-rook'); break;
                        case '傌':
                            tmp[i * 9 + j].classList.add('piece-red-knight'); break;
                        case '相':
                            tmp[i * 9 + j].classList.add('piece-red-minister'); break;
                        case '仕':
                            tmp[i * 9 + j].classList.add('piece-red-guard'); break;
                        case '帥':
                            tmp[i * 9 + j].classList.add('piece-red-commander'); break;
                        case '炮':
                            tmp[i * 9 + j].classList.add('piece-red-cannon'); break;
                        case '兵':
                            tmp[i * 9 + j].classList.add('piece-red-pawn'); break;
                        case '車':
                            tmp[i * 9 + j].classList.add('piece-black-rook'); break;
                        case '馬':
                            tmp[i * 9 + j].classList.add('piece-black-knight'); break;
                        case '象':
                            tmp[i * 9 + j].classList.add('piece-black-minister'); break;
                        case '士':
                            tmp[i * 9 + j].classList.add('piece-black-guard'); break;
                        case '將':
                            tmp[i * 9 + j].classList.add('piece-black-commander'); break;
                        case '包':
                            tmp[i * 9 + j].classList.add('piece-black-cannon'); break;
                        case '卒':
                            tmp[i * 9 + j].classList.add('piece-black-pawn'); break;
                        default:
                            break;
                    }
                }
            }
        }

        function updateEatenState(eaten='') {
            let myeaten = null;
            let anothereaten = null;
            if(myRealm == 'SP') {
                myeaten = "self";
                anothereaten = "opponent";
            } else if(myRealm == 'NSP') {
                myeaten = "opponent";
                anothereaten = "self";
            }

            let mytmp = document.getElementById('eaten-' + myeaten).getElementsByTagName('span');
            let anothertmp = document.getElementById('eaten-' + anothereaten).getElementsByTagName('span');
            for(let i=0, mycount=0, anothercount = 0; i<eaten.length; i++) {
                switch (eaten[i]) {
                    case '俥':
                        mytmp[mycount++].classList.add('piece-red-rook'); break;
                    case '傌':
                        mytmp[mycount++].classList.add('piece-red-knight'); break;
                    case '相':
                        mytmp[mycount++].classList.add('piece-red-minister'); break;
                    case '仕':
                        mytmp[mycount++].classList.add('piece-red-guard'); break;
                    case '帥':
                        mytmp[mycount++].classList.add('piece-red-commander'); break;
                    case '炮':
                        mytmp[mycount++].classList.add('piece-red-cannon'); break;
                    case '兵':
                        mytmp[mycount++].classList.add('piece-red-pawn'); break;
                    case '車':
                        anothertmp[anothercount++].classList.add('piece-black-rook'); break;
                    case '馬':
                        anothertmp[anothercount++].classList.add('piece-black-knight'); break;
                    case '象':
                        anothertmp[anothercount++].classList.add('piece-black-minister'); break;
                    case '士':
                        anothertmp[anothercount++].classList.add('piece-black-guard'); break;
                    case '將':
                        anothertmp[anothercount++].classList.add('piece-black-commander'); break;
                    case '包':
                        anothertmp[anothercount++].classList.add('piece-black-cannon'); break;
                    case '卒':
                        anothertmp[anothercount++].classList.add('piece-black-pawn'); break;
                    default:
                        break;
                }
            }
        }

        let move_par = '';
        function choose(x, y) {
            if (move_par == '') {
                move_par += x + y;
                document.getElementById(x + '-' + y).style.borderStyle = 'double';
            }
            else if (move_par == '' + x + y) {
                document.getElementById(x + '-' + y).style.borderStyle = 'none';
                move_par = '';
            }
            else if (move_par.length > 2) { move_par = ''; }
            else {
                document.getElementById(move_par[0] + '-' + move_par[1]).style.borderStyle = 'none';
                move('launch', (move_par += x + y));
                move_par = '';
            }
        }

        function move(state = 'launch', par = move_par) {
            let states = state.split(';');

            if (states[0] == 'launch') {
                php("move=" + par, "process.php", move);
            }
            else if (states[0] == 'success') {
                updateBoardState(states[1]);
            } else {
                alert(state);
            }
        }

        function updateInfoState(myRealm, nowRealm, stepTimeLeft, timeLeft_SP, timeLeft_NSP, stepTime) {
            stepTimeLeft = parseInt(stepTimeLeft);
            timeLeft_SP = parseInt(timeLeft_SP);
            timeLeft_NSP = parseInt(timeLeft_NSP);
            stepTime = parseInt(stepTime);

            stepTimeLeft = Math.floor(stepTimeLeft/60) + ":" + stepTimeLeft%60;
            timeLeft_SP = Math.floor(timeLeft_SP/60) + ":" + timeLeft_SP%60;
            timeLeft_NSP = Math.floor(timeLeft_NSP/60) + ":" + timeLeft_NSP%60;
            stepTime = Math.floor(stepTime/60) + ":" + stepTime%60;

            let myinfo = null;
            let anotherinfo = null;
            if(myRealm == 'SP') {
                myinfo = "self";
                anotherinfo = "opponent";
            } else if(myRealm == 'NSP') {
                myinfo = "opponent";
                anotherinfo = "self";
            }

            document.getElementById('info-' + myinfo).getElementsByClassName('info-totaltime')[0].innerHTML = timeLeft_SP;
            document.getElementById('info-' + anotherinfo).getElementsByClassName('info-totaltime')[0].innerHTML = timeLeft_NSP;
            if(nowRealm == "NSP") {
                let tmp = myinfo;
                myinfo = anotherinfo;
                anotherinfo = tmp;
            }
            document.getElementById('info-' + myinfo).getElementsByClassName('info-steptime')[0].innerHTML = stepTimeLeft;
            document.getElementById('info-' + anotherinfo).getElementsByClassName('info-steptime')[0].innerHTML = stepTime;
            document.getElementById('info-' + myinfo).getElementsByClassName('info-image')[0].style.boxShadow = "0px 0px 10px rgb(255, 255, 255)";
            document.getElementById('info-' + anotherinfo).getElementsByClassName('info-image')[0].style.boxShadow = "0px 0px 10px rgb(0, 0, 0)";
        }

        function check(state = 'launch') {
            let states = state.split(';');

            if (states[0] == 'launch') {
                php("check=game", "process.php", check);
            }
            else if (states[0] == 'success') {
                updateBoardState(states[1]);
                setTimeout(check, 1000);
                updateInfoState(myRealm, (states[1].length/4%2==0 ? 'SP' : 'NSP'), states[3], states[4], states[5], states[6]);
                updateEatenState(states[2]);
            } else if(states[0] == 'end') {
                alert('Game Over!');
                window.location.href = "index.php";
            } else {
                alert(state + "; Connect error! Please refresh this page.");
            }
        }

        setTimeout(check, 10);

        ///

        window.onresize = function () { sizeChessBoard(); };

            function sizeChessBoard() {
                let w = document.getElementById('board-container').clientWidth;
                let h = document.getElementById('board-container').clientHeight;

                let border = 5;
                let board_w = 5154;
                let board_h = 5671;
                let chessboard = document.getElementById('chessboard');
                if (w*board_h > h*board_w) {
                    chessboard.style.height = h - border*2 + 'px';
                    chessboard.style.width = h * board_w / board_h - border*2 + 'px';
                } else {
                    chessboard.style.height = w * board_h / board_w - border*2 + 'px';
                    chessboard.style.width = w - border*2 + 'px';
                }


                let main = document.getElementsByTagName('main')[0];
                let self_eaten = document.getElementById('eaten-self');
                let opponent_eaten = document.getElementById('eaten-opponent');
                let boards = document.getElementsByTagName('article');
                let info_boards = document.getElementsByClassName('info-board');
                if(window.innerWidth > window.innerHeight) {
                    main.style.flexDirection = 'row';
                    self_eaten.style.display = 'flex';
                    opponent_eaten.style.display = 'flex';
                    boards[0].style.width = '20%';
                    boards[1].style.width = '50%';
                    boards[2].style.width = '20%';
                    info_boards[0].style.height = '17.5%';
                    info_boards[1].style.height = '17.5%';
                    for(let i = 0; i < 3; i++) {
                        boards[i].style.height = '100%';
                    }
                } else {
                    main.style.flexDirection = 'column';
                    self_eaten.style.display = 'none';
                    opponent_eaten.style.display = 'none';
                    boards[0].style.width = '50%';
                    boards[1].style.width = '100%';
                    boards[2].style.width = '50%';
                    boards[0].style.height = '20%';
                    boards[1].style.height = '50%';
                    boards[2].style.height = '20%';
                    info_boards[0].style.height = '100%';
                    info_boards[1].style.height = '100%';
                }
            }
            
            sizeChessBoard();
    </script>
</body>
</html>