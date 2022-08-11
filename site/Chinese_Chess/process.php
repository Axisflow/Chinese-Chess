<?php
    define('APP_ROOT', "../");

    require_once 'rule.php';
    require_once 'check.php';
    require_once APP_ROOT.'module/datetime.php';
    require_once APP_ROOT.'account/check.php';

    if (session_status() == PHP_SESSION_NONE) session_start();

    if(array_key_exists('move', $_POST)) {
        $move = $_POST['move'];
        $x1 = $move[0];
        $y1 = $move[1];
        $x2 = $move[2];
        $y2 = $move[3];

        $PlayID = getLastPlay(getAccountName());
        $PlayState = getPlayState($PlayID);

        if(explode(";", $PlayState)[0] == "End"){ //如果不是遊戲中
            echo 'end';
            exit;
        }

        $realm = (strlen(getStep($PlayID))/4%2==0 ? 'SP' : 'NSP');
        if($realm == 'SP') {
            if(getSP_UUID($PlayID) != getUUID(getAccountName())){ //如果不是自己的棋子
                echo 'Not your turn';
                exit;
            }
        } else if($realm == 'NSP') {
            if(getNSP_UUID($PlayID) != getUUID(getAccountName())){
                echo 'Not your turn';
                exit;
            }
        }

        $chessBoard = new ChessBoard(getStep($PlayID), getEaten($PlayID));
        $result = $chessBoard->move($x1, $y1, $x2, $y2);
        if($result){
            $passtime = substractTimeUS(getDateTimeUS(), getLastMove($PlayID));

            $left_steptime = getStepTime($PlayID)*1000000 - $passtime;
            $left_SP_totaltime = getSP_TimeLeft($PlayID);
            $left_NSP_totaltime = getNSP_TimeLeft($PlayID);
            if($left_steptime <= 0) {
                $left_steptime = 0;
                setPlayState($PlayID, "End;".$realm." Timeout");
                echo 'end';
                exit;
            } else {
                if($realm == 'SP') {
                    $left_SP_totaltime -= $passtime;
                    if($left_SP_totaltime <= 0) {
                        $left_SP_totaltime = 0;
                    }
                    setSP_TimeLeft($PlayID, $left_SP_totaltime);
                } else if($realm == 'NSP') {
                    $left_NSP_totaltime -= $passtime;
                    if($left_NSP_totaltime <= 0) {
                        $left_NSP_totaltime = 0;
                    }
                    setNSP_TimeLeft($PlayID, $left_NSP_totaltime);
                }
            }
            setLastMove($PlayID, getDateTimeUS());

            $_step = $chessBoard->step;
            $_eaten = $chessBoard->getEaten();
            $_state = $chessBoard->state;

            setStep($PlayID, $_step);
            setEaten($PlayID, $_eaten);
            if($_state['game'] == GameState::End) setPlayState($PlayID, "End");

            echo 'success;'.$_step.';'.$_eaten;
        } else {
            echo 'Move error!';
        }
    }
    else if(array_key_exists('start', $_POST)) {
        $start = $_POST['start'];
        if($start == 'true') {
            $PlayID = createPlay();
            joinPlayer($PlayID, $_SESSION['AccountName']);
            setLastPlay($_SESSION['AccountName'], $PlayID);
            echo 'success;'.getSP_UUID($PlayID).';'.getNSP_UUID($PlayID).';'.$PlayID;
        }
    }
    else if(array_key_exists('join', $_POST)) {
        $PlayID = $_POST['join'];

        if(getSP_UUID($PlayID) == getUUID($_SESSION['AccountName']) || getNSP_UUID($PlayID) == getUUID($_SESSION['AccountName'])) {
            echo 'fail';
            exit;
        }

        $state = joinPlayer($PlayID, $_SESSION['AccountName']);

        if($state) {
            setLastPlay($_SESSION['AccountName'], $PlayID);
            startGame($PlayID, 2400, 300, 60);
            echo 'success;'.getSP_UUID($PlayID).';'.getNSP_UUID($PlayID).';'.$PlayID;
        } else {
            echo 'fail';
        }
    }
    else if(array_key_exists('check', $_POST)) {
        $PlayID = getLastPlay(getAccountName());
        if($_POST['check'] == 'room') {
            $_SP_UUID = getSP_UUID($PlayID);
            $_NSP_UUID = getNSP_UUID($PlayID);
            echo 'success;'.($_SP_UUID==''?'':getAccountNameByUUID($_SP_UUID)).';'.($_NSP_UUID==''?'':getAccountNameByUUID($_NSP_UUID)).';'.$PlayID;
        } else if($_POST['check'] == 'game') {
            $PlayID = getLastPlay(getAccountName());
            $PlayState = getPlayState($PlayID);

            if(explode(";", $PlayState)[0] == "End"){ //如果不是遊戲中
                echo 'end';
                exit;
            }

            $realm = (strlen(getStep($PlayID))/4%2==0 ? 'SP' : 'NSP');
            $passtime = substractTimeUS(getDateTimeUS(), getLastMove($PlayID));

            $left_steptime = getStepTime($PlayID)*1000000 - $passtime;
            $left_SP_totaltime = getSP_TimeLeft($PlayID);
            $left_NSP_totaltime = getNSP_TimeLeft($PlayID);
            if($left_steptime <= 0) {
                $left_steptime = 0;
                setPlayState($PlayID, "End;".$realm." Timeout");
                echo 'end';
                exit;
            } else {
                if($realm == 'SP') {
                    $left_SP_totaltime -= $passtime;
                    if($left_SP_totaltime <= 0) {
                        $left_SP_totaltime = 0;
                    }
                } else if($realm == 'NSP') {
                    $left_NSP_totaltime -= $passtime;
                    if($left_NSP_totaltime <= 0) {
                        $left_NSP_totaltime = 0;
                    }
                }
            }

            echo 'success;'.getStep($PlayID).';'.getEaten($PlayID).';'.(int)floor($left_steptime/1000000).';'.(int)floor($left_SP_totaltime/1000000).';'.(int)floor($left_NSP_totaltime/1000000).';'.getStepTime($PlayID); // Step; Eaten; Step_TimeLeft; SP_TimeLeft; NSP_TimeLeft; StepTime
        }
    }

    

?>