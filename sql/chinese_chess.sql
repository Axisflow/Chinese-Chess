-- --------------------------------------------------------
-- 主機:                           192.168.195.53
-- 伺服器版本:                        10.6.8-MariaDB - mariadb.org binary distribution
-- 伺服器作業系統:                      Win64
-- HeidiSQL 版本:                  11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- 傾印 chinese_chess 的資料庫結構
CREATE DATABASE IF NOT EXISTS `chinese_chess` /*!40100 DEFAULT CHARACTER SET utf8mb3 */;
USE `chinese_chess`;

-- 傾印  資料表 chinese_chess.0 結構
CREATE TABLE IF NOT EXISTS `0` (
  `PlayID` int(11) unsigned NOT NULL,
  `State` char(50) NOT NULL,
  `CreateTime` datetime DEFAULT NULL,
  `SP_UUID` char(32) DEFAULT NULL,
  `NSP_UUID` char(32) DEFAULT NULL,
  `TotalTime` int(10) DEFAULT NULL,
  `StepTime` smallint(5) DEFAULT NULL,
  `FinalTime` smallint(5) DEFAULT NULL,
  `Step` text NOT NULL DEFAULT '',
  `Eaten` text NOT NULL DEFAULT '',
  `LastMove` char(26) DEFAULT NULL,
  `SP_TimeLeft` bigint(20) DEFAULT NULL,
  `NSP_TimeLeft` bigint(20) DEFAULT NULL,
  `Chat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Chat`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='TotalTime: (sec)\r\nStepTime: (sec)\r\nFinalTime: (sec)\r\nTimeLeft: (µs)\r\nLastMove: (yyyy-mm-dd hh:mm:ss.µsµsµs)';

-- 取消選取資料匯出。

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
