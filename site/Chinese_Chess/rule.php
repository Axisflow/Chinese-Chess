<?php
    enum ChessType {
        case Commander;
        case Guard;
        case Minister;
        case Rook;
        case Knight;
        case Cannon;
        case Pawn;
    }

    enum GameState {
        case Playing;
        case End;
        case Draw;
        case SP_Win;
        case NSP_Win;
    }

    class PieceChess {
        public ChessType $type;
        public bool $realm; // true for SP, false for NSP.

        public function __construct(ChessType $type, bool $realm) {
            $this->type = $type;
            $this->realm = $realm;
        }
    }

    class ChessBoard {
        public string $step;
        public $eaten = array();
        public $state = array('game' => GameState::Playing, 'winner' => null);
        public $board = array(
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
                array()
            );

        public function __construct(string $step, string $eaten) {
            $this->step = ($step==null ? "" : $step);

            $chessboard = array(
                array("俥", "傌", "相", "仕", "帥", "仕", "相", "傌", "俥"),
                array("　", "　", "　", "　", "　", "　", "　", "　", "　"),
                array("　", "炮", "　", "　", "　", "　", "　", "炮", "　"),
                array("兵", "　", "兵", "　", "兵", "　", "兵", "　", "兵"),
                array("　", "　", "　", "　", "　", "　", "　", "　", "　"),
                array("　", "　", "　", "　", "　", "　", "　", "　", "　"),
                array("卒", "　", "卒", "　", "卒", "　", "卒", "　", "卒"),
                array("　", "包", "　", "　", "　", "　", "　", "包", "　"),
                array("　", "　", "　", "　", "　", "　", "　", "　", "　"),
                array("車", "馬", "象", "士", "將", "士", "象", "馬", "車")
            );
            for($i = 0; $i < strlen($step); $i+=4) {
                $chessboard[$step[$i + 3]][$step[$i + 2]] = $chessboard[$step[$i + 1]][$step[$i]];
                $chessboard[$step[$i + 1]][$step[$i]] = "　";
            }

            for ($i = 0; $i < 10; $i++) {
                for ($j = 0; $j < 9; $j++) {
                    switch ($chessboard[$i][$j]) {
                        case '俥':
                            $this->board[$i][] = new PieceChess(ChessType::Rook, false); break;
                        case '傌':
                            $this->board[$i][] = new PieceChess(ChessType::Knight, false); break;
                        case '相':
                            $this->board[$i][] = new PieceChess(ChessType::Minister, false); break;
                        case '仕':
                            $this->board[$i][] = new PieceChess(ChessType::Guard, false); break;
                        case '帥':
                            $this->board[$i][] = new PieceChess(ChessType::Commander, false); break;
                        case '炮':
                            $this->board[$i][] = new PieceChess(ChessType::Cannon, false); break;
                        case '兵':
                            $this->board[$i][] = new PieceChess(ChessType::Pawn, false); break;
                        case '車':
                            $this->board[$i][] = new PieceChess(ChessType::Rook, true); break;
                        case '馬':
                            $this->board[$i][] = new PieceChess(ChessType::Knight, true); break;
                        case '象':
                            $this->board[$i][] = new PieceChess(ChessType::Minister, true); break;
                        case '士':
                            $this->board[$i][] = new PieceChess(ChessType::Guard, true); break;
                        case '將':
                            $this->board[$i][] = new PieceChess(ChessType::Commander, true); break;
                        case '包':
                            $this->board[$i][] = new PieceChess(ChessType::Cannon, true); break;
                        case '卒':
                            $this->board[$i][] = new PieceChess(ChessType::Pawn, true); break;
                        case '　':
                            $this->board[$i][] = null; break;
                        default:
                            break;
                    }
                }
            }

            for($i = 0; $i < strlen($eaten); $i+=3) { // 警告：中文編碼有問題，暫時用這個方法解決
                switch ($eaten[$i].$eaten[$i+1].$eaten[$i+2]) {
                    case '俥':
                        $this->eaten[] = new PieceChess(ChessType::Rook, false); break;
                    case '傌':
                        $this->eaten[] = new PieceChess(ChessType::Knight, false); break;
                    case '相':
                        $this->eaten[] = new PieceChess(ChessType::Minister, false); break;
                    case '仕':
                        $this->eaten[] = new PieceChess(ChessType::Guard, false); break;
                    case '帥':
                        $this->eaten[] = new PieceChess(ChessType::Commander, false); break;
                    case '炮':
                        $this->eaten[] = new PieceChess(ChessType::Cannon, false); break;
                    case '兵':
                        $this->eaten[] = new PieceChess(ChessType::Pawn, false); break;
                    case '車':
                        $this->eaten[] = new PieceChess(ChessType::Rook, true); break;
                    case '馬':
                        $this->eaten[] = new PieceChess(ChessType::Knight, true); break;
                    case '象':
                        $this->eaten[] = new PieceChess(ChessType::Minister, true); break;
                    case '士':
                        $this->eaten[] = new PieceChess(ChessType::Guard, true); break;
                    case '將':
                        $this->eaten[] = new PieceChess(ChessType::Commander, true); break;
                    case '包':
                        $this->eaten[] = new PieceChess(ChessType::Cannon, true); break;
                    case '卒':
                        $this->eaten[] = new PieceChess(ChessType::Pawn, true); break;
                    case '　':
                        $this->eaten[] = null; break;
                    default:
                        break;
                }
            }

            $this->checkWinner();
        }
        
        public function getEaten(): string {
            $eaten = "";
            for($i = 0; $i < count($this->eaten); $i++) {
                if ($this->eaten[$i] == null) {
                    $eaten .= '　';
                } else {
                    switch($this->eaten[$i]->type) {
                        case ChessType::Rook:
                            $eaten .= ($this->eaten[$i]->realm?"車":"俥"); break;
                        case ChessType::Knight:
                            $eaten .= ($this->eaten[$i]->realm?"馬":"傌"); break;
                        case ChessType::Minister:
                            $eaten .= ($this->eaten[$i]->realm?"象":"相"); break;
                        case ChessType::Guard:
                            $eaten .= ($this->eaten[$i]->realm?"士":"仕"); break;
                        case ChessType::Commander:
                            $eaten .= ($this->eaten[$i]->realm?"將":"帥"); break;
                        case ChessType::Cannon:
                            $eaten .= ($this->eaten[$i]->realm?"包":"炮"); break;
                        case ChessType::Pawn:
                            $eaten .= ($this->eaten[$i]->realm?"卒":"兵"); break;
                        default:
                            break;
                    }
                }
            }
            return $eaten;
        }

        protected function checkWinner(): void {
            $ExistSPCommander = false;
            $ExistNSPCommander = false;
            for ($i = 0; $i < 10; $i++) {
                for ($j = 0; $j < 9; $j++) {
                    if ($this->board[$i][$j] != null) {
                        if ($this->board[$i][$j]->type == ChessType::Commander) {
                            if ($this->board[$i][$j]->realm) {
                                $ExistSPCommander = true;
                            } else {
                                $ExistNSPCommander = true;
                            }
                        }
                    }
                }
            }

            if (!$ExistSPCommander && !$ExistNSPCommander) {
                $this->state['game'] = GameState::End;
                $this->state['winner'] = GameState::Draw;
            } else {
                if (!$ExistSPCommander) {
                    $this->state['game'] = GameState::End;
                    $this->state['winner'] = GameState::NSP_Win;
                } else if (!$ExistNSPCommander) {
                    $this->state['game'] = GameState::End;
                    $this->state['winner'] = GameState::SP_Win;
                }
            }
        } 

        public function move(int $c1, int $r1, int $c2, int $r2): bool {
            $this->checkWinner();
            if ($this->state['game'] != GameState::Playing) {
                return false;
            }

            $realm = strlen($this->step)/4 % 2 == 0;

            if($this->board[$r1][$c1] == null || $c1<0 || $c1>8 || $r1<0 || $r1>9 || $c2<0 || $c2>8 || $r2<0 || $r2>9 || $realm != $this->board[$r1][$c1]->realm || ($c1 == $c2 && $r1 == $r2)) {
                return false;
            }

            if($this->board[$r2][$c2] != null){
                if($this->board[$r1][$c1]->realm == $this->board[$r2][$c2]->realm) return false;
            }

            $correct = null;
            switch($this->board[$r1][$c1]->type) {
                case ChessType::Rook:
                    $correct = $this->moveRook($c1, $r1, $c2, $r2); break;
                case ChessType::Knight:
                    $correct = $this->moveKnight($c1, $r1, $c2, $r2); break;
                case ChessType::Minister:
                    $correct = $this->moveMinister($c1, $r1, $c2, $r2); break;
                case ChessType::Guard:
                    $correct = $this->moveGuard($c1, $r1, $c2, $r2); break;
                case ChessType::Commander:
                    $correct = $this->moveCommander($c1, $r1, $c2, $r2); break;
                case ChessType::Cannon:
                    $correct = $this->moveCannon($c1, $r1, $c2, $r2); break;
                case ChessType::Pawn:
                    $correct = $this->movePawn($c1, $r1, $c2, $r2); break;
                default:
                    break;
            }

            if($correct) {
                $this->eaten[] = $this->board[$r2][$c2];
                $this->board[$r2][$c2] = $this->board[$r1][$c1];
                $this->board[$r1][$c1] = null;
                $this->step = $this->step . $c1 . $r1 . $c2 . $r2;

                $this->checkWinner();
                return true;
            }

            return false;
        }

        protected function moveRook(int $c1, int $r1, int $c2, int $r2): bool {
            if($c1 == $c2) {
                if($r1 > $r2) {
                    for($i=$r1-1; $i>$r2; $i--) {
                        if($this->board[$i][$c1] != null) {
                            return false;
                        }
                    }
                } else {
                    for($i=$r1+1; $i<$r2; $i++) {
                        if($this->board[$i][$c1] != null) {
                            return false;
                        }
                    }
                }
                return true;
            }
            else if($r1 == $r2) {
                if($c1 > $c2) {
                    for($i=$c1-1; $i>$c2; $i--) {
                        if($this->board[$r1][$i] != null) {
                            return false;
                        }
                    }
                } else {
                    for($i=$c1+1; $i<$c2; $i++) {
                        if($this->board[$r1][$i] != null) {
                            return false;
                        }
                    }
                }

                return true;
            }
            
            return false;
        }

        protected function moveKnight(int $c1, int $r1, int $c2, int $r2): bool {
            if(abs($c1-$c2) == 2 && abs($r1-$r2) == 1) {
                if($this->board[$r1][($c1+$c2)/2] != null) {
                    return false;
                }
                return true;
            }
            else if(abs($c1-$c2) == 1 && abs($r1-$r2) == 2) {
                if($this->board[($r1+$r2)/2][$c1] != null) {
                    return false;
                }
                return true;
            }

            return false;
        }

        protected function moveMinister(int $c1, int $r1, int $c2, int $r2): bool {
            if(abs($c1-$c2) == 2 && abs($r1-$r2) == 2 && $this->board[($r1+$r2)/2][($c1+$c2)/2] == null) {
                if($this->board[$r1][$c1]->realm) {
                    if($r2 >= 5 && $r1 >= 5) {
                        return true;
                    }
                } else {
                    if($r2 <= 4 && $r1 <= 4) {
                        return true;
                    }
                }
            }

            return false;
        }

        protected function moveGuard(int $c1, int $r1, int $c2, int $r2): bool {
            if($c1 > 2 && $c1 < 6 && $c2 > 2 && $c2 < 6 && abs($r1-$r2) == 1 && abs($c1-$c2) == 1) {
                if($this->board[$r1][$c1]->realm) {
                    if($r1 >=7 && $r1 <=9 && $r2 >=7 && $r2 <=9) {
                        return true;
                    }
                }
                else {
                    if($r1 >=0 && $r1 <=2 && $r2 >=0 && $r2 <=2) {
                        return true;
                    }
                }
            }

            return false;
        }

        protected function moveCommander(int $c1, int $r1, int $c2, int $r2): bool {
            if($c1 > 2 && $c1 < 6 && $c2 > 2 && $c2 < 6 && ((abs($c1-$c2) == 1 && $r1 == $r2) || (abs($r1-$r2) == 1 && $c1 == $c2))) {
                if($this->board[$r1][$c1]->realm) {
                    if($r1 >=7 && $r1 <=9 && $r2 >=7 && $r2 <=9) {
                        return true;
                    }
                }
                else {
                    if($r1 >=0 && $r1 <=2 && $r2 >=0 && $r2 <=2) {
                        return true;
                    }
                }
            } else if($this->board[$r2][$c2] != null) {
                if($c1 == $c2 && $this->board[$r1][$c1]->realm == !$this->board[$r2][$c2]->realm && $this->board[$r2][$c2]->type == ChessType::Commander) {
                    if($r1 > $r2) {
                        for($i=$r1-1; $i>$r2; $i--) {
                            if($this->board[$i][$c1] != null) {
                                return false;
                            }
                        }
                    } else {
                        for($i=$r1+1; $i<$r2; $i++) {
                            if($this->board[$i][$c1] != null) {
                                return false;
                            }
                        }
                    }
                    return true;
                }
            }

            return false;
        }

        protected function moveCannon(int $c1, int $r1, int $c2, int $r2): bool {
            $canJump = false;
            if($c1 == $c2) {
                if($r1 > $r2) {
                    for($i=$r1-1; $i>$r2; $i--) {
                        if($this->board[$i][$c1] != null) {
                            if(!$canJump) {
                                $canJump = true;
                            }
                            else if ($canJump) {
                                return false;
                            }
                        }
                    }
                } else {
                    for($i=$r1+1; $i<$r2; $i++) {
                        if($this->board[$i][$c1] != null) {
                            if(!$canJump) {
                                $canJump = true;
                            }
                            else if ($canJump) {
                                return false;
                            }
                        }
                    }
                }
            }
            else if($r1 == $r2) {
                if($c1 > $c2) {
                    for($i=$c1-1; $i>$c2; $i--) {
                        if($this->board[$r1][$i] != null) {
                            if(!$canJump) {
                                $canJump = true;
                            }
                            else if ($canJump) {
                                return false;
                            }
                        }
                    }
                } else {
                    for($i=$c1+1; $i<$c2; $i++) {
                        if($this->board[$r1][$i] != null) {
                            if(!$canJump) {
                                $canJump = true;
                            }
                            else if ($canJump) {
                                return false;
                            }
                        }
                    }
                }
            } else {
                return false;
            }

            if(!$canJump && $this->board[$r2][$c2] == null) {
                return true;
            }
            else if($canJump && $this->board[$r2][$c2] != null) {
                return true;
            }

            return false;
        }

        protected function movePawn(int $c1, int $r1, int $c2, int $r2): bool {
            if($this->board[$r1][$c1]->realm == true) {
                if($r1 > 4) {
                    if($c1 == $c2 && $r2 == $r1-1) {
                        return true;
                    }
                }
                else {
                    if($c1 == $c2 && $r2 == $r1-1) {
                        return true;
                    }
                    else if(($c2 == $c1+1 || $c2 == $c1-1) && $r1 == $r2) {
                        return true;
                    }
                }
            } else {
                if($r1 < 5) {
                    if($c1 == $c2 && $r2 == $r1+1) {
                        return true;
                    }
                }
                else {
                    if($c1 == $c2 && $r2 == $r1+1) {
                        return true;
                    }
                    else if(($c2 == $c1+1 || $c2 == $c1-1) && $r1 == $r2) {
                        return true;
                    }
                }
            }

            return false;
        }
    }


?>