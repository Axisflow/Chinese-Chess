<?php
!defined('DB_PLAY_TYPE') && define('DB_PLAY_TYPE', 'mysql');
!defined('DB_PLAY_HOST') && define('DB_PLAY_HOST', 'localhost');
!defined('DB_PLAY_USER') && define('DB_PLAY_USER', '');
!defined('DB_PLAY_PASS') && define('DB_PLAY_PASS', '');
!defined('DB_PLAY_NAME') && define('DB_PLAY_NAME', 'chinese_chess');
!defined('DB_PLAY_NOW_TABLE') && define('DB_PLAY_NOW_TABLE', '0');

!defined('DB_PLAY_RECORD_TABLE') && define('DB_PLAY_RECORD_TABLE', 'record');

!defined('APP_ROOT') && define('APP_ROOT', "../");

require_once APP_ROOT.'account/check.php';
require_once APP_ROOT.'module/datetime.php';

if (session_status() == PHP_SESSION_NONE) session_start();

function updateCookiePlayInfo() {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);
    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `AccountName` = '".$_SESSION['AccountName']."'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    setcookie('AccountName', $row['AccountName'], time() + (86400 * 30), "/");
    setcookie('Token', $row['Token'], time() + (86400 * 30), "/");
}

function updateSessionPlayInfo() {
    if(!isset($_COOKIE['PlayID'])) {
        updateCookiePlayInfo();
    }

    $_SESSION['PlayID'] = $_COOKIE['PlayID'];
    $_SESSION['State'] = $_COOKIE['State'];
}

function getSP_UUID($playID){
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);
    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '".$playID."'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['SP_UUID'];
}

function getNSP_UUID($playID){
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);
    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '".$playID."'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['NSP_UUID'];
}

function createPlay() {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $playID = null;
    while(true) {
        $playID = random_int(-2147483647, 2147483647) + 2147483647;
        $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        if(!$row) {
            break;
        }
    }

    $cmd = "INSERT INTO `".DB_PLAY_NOW_TABLE."` (`PlayID`, `State`, `CreateTime`) VALUES ('".$playID."', 'PRE-0-0', '".date('Y-m-d H:i:s')."')";
    $db->query($cmd);

    return $playID;
}

function joinPlayer($playID, $accountName) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    if($result->rowCount() == 0) return false;
    $row = $result->fetch();

    $states = explode('-', $row['State']);
    if($states[0] == 'PRE') {
        $SP_State = $states[1];
        $NSP_State = $states[2];

        $choosen = random_int(0, 1); // 0: SP, 1: NSP
        if($SP_State == '1' && $NSP_State == '1') {
            $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `State` = 'Playing' WHERE `PlayID` = '$playID'";
            $db->query($cmd);
            return false;
        } else if($SP_State == '1') {
            $choosen = 1;
        } else if($NSP_State == '1') {
            $choosen = 0;
        }

        if($choosen == 0) {
            $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `State` = 'PRE-1-$NSP_State' WHERE `PlayID` = '$playID'";
            $db->query($cmd);
            $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `SP_UUID` = '".getUUID($accountName)."' WHERE `PlayID` = '$playID'";
            $db->query($cmd);
        } else if($choosen == 1) {
            $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `State` = 'PRE-$SP_State-1' WHERE `PlayID` = '$playID'";
            $db->query($cmd);
            $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `NSP_UUID` = '".getUUID($accountName)."' WHERE `PlayID` = '$playID'";
            $db->query($cmd);
        }
    } else return false;

    return true;
}

function getStep($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['Step'];
}

function getEaten($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['Eaten'];
}

function setPlayState($playID, $state) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `State` = '$state' WHERE `PlayID` = '$playID'";
    $db->query($cmd);
}

function setStep($playID, $step) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `Step` = '$step' WHERE `PlayID` = '$playID'";
    $db->query($cmd);
}

function setEaten($playID, $eaten) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `Eaten` = '$eaten' WHERE `PlayID` = '$playID'";
    $db->query($cmd);
}

function getPlayState($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['State'];
}

function checkOrder($accountName ,$playID) {
    switch(getUUID($accountName)) {
        case getNSP_UUID($playID):
            return 'NSP';
        case getSP_UUID($playID):
            return 'SP';
        default:
            return null;
    }
}

function startGame($PlayID, $TotalTime, $StepTime, $FinalTime) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$PlayID'";
    $result = $db->query($cmd);
    $row = $result->fetch();

    $LastMove = getDateTimeUS();
    $TimeLeft = $TotalTime*1000000;
    if($row['State'] == 'PRE-1-1') {
        $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `State` = 'Playing', `LastMove` = '$LastMove', `TotalTime` = '$TotalTime', `StepTime` = '$StepTime', `SP_TimeLeft` = '$TimeLeft', `NSP_TimeLeft` = '$TimeLeft' WHERE `PlayID` = '$PlayID'";
        $db->query($cmd);
    }
}

function getStepTime($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['StepTime'];
}

function getTotalTime($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['TotalTime'];
}

function getLastMove($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['LastMove'];
}

function setLastMove($playID, $lastMove) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `LastMove` = '$lastMove' WHERE `PlayID` = '$playID'";
    $db->query($cmd);
}

function getSP_TimeLeft($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['SP_TimeLeft'];
}

function getNSP_TimeLeft($playID) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "SELECT * FROM `".DB_PLAY_NOW_TABLE."` WHERE `PlayID` = '$playID'";
    $result = $db->query($cmd);
    $row = $result->fetch();
    return $row['NSP_TimeLeft'];
}

function setSP_TimeLeft($playID, $timeLeft) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `SP_TimeLeft` = '$timeLeft' WHERE `PlayID` = '$playID'";
    $db->query($cmd);
}

function setNSP_TimeLeft($playID, $timeLeft) {
    $db = new PDO(DB_PLAY_TYPE.':host='.DB_PLAY_HOST.';dbname='.DB_PLAY_NAME, DB_PLAY_USER, DB_PLAY_PASS);

    $cmd = "UPDATE `".DB_PLAY_NOW_TABLE."` SET `NSP_TimeLeft` = '$timeLeft' WHERE `PlayID` = '$playID'";
    $db->query($cmd);
}

function checkNowRealm($playID) {
    return (strlen(getStep($playID))/4%2==0 ? 'SP' : 'NSP');
}

?>