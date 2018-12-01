<?php

namespace Phpokoban;

class Game
{
    public const BLOCK_WALL = '#';
    public const BLOCK_BOX = '$';
    public const BLOCK_GOAL = '.';
    public const BLOCK_BOX_ON_GOAL = '*';
    public const BLOCK_EMPTY = ' ';
    public const PLAYER_POS = '@';
    public const PLAYER_POS_ON_GOAL = '+';

    /** @var string[][] */
    private $game = [];
    /** @var int */
    private $height = 0;
    /** @var int */
    private $width = 0;
    /** @var int */
    private $player_row = 0;
    /** @var int */
    private $player_col = 0;

    /**
     * @param string $grid []
     */
    public function __construct(array $grid)
    {
        $this->height = count($grid);
        foreach ($grid as &$line) {
            $row = [];
            $this->width = max($this->width, strlen($line));
            for ($i = 0; $i < strlen($line); ++$i) {
                switch ($line[$i]) {
                    case self::PLAYER_POS:
                    case self::PLAYER_POS_ON_GOAL:
                        $this->player_row = count($this->game);
                        $this->player_col = $i;
                        $block = $line[$i] === self::PLAYER_POS ? self::BLOCK_EMPTY : self::BLOCK_GOAL;
                        break;
                    case self::BLOCK_EMPTY:
                    case self::BLOCK_GOAL:
                    case self::BLOCK_WALL:
                    case self::BLOCK_BOX:
                    case self::BLOCK_BOX_ON_GOAL:
                        $block = $line[$i];
                        break;
                    default:
                        $block = self::BLOCK_EMPTY;
                }
                $row[] = $block;
            }
            $this->game[] = $row;
        }
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function player(): Position
    {
        return new Position($this->player_col, $this->player_row);
    }

    public function moveUp()
    {
        $nextBlock = &$this->game[$this->player_row - 1][$this->player_col];
        $nextNextBlock = &$this->game[$this->player_row - 2][$this->player_col];
        if ($this->pushBox($nextBlock, $nextNextBlock)) {
            --$this->player_row;
        }
    }

    public function moveDown()
    {
        $nextBlock = &$this->game[$this->player_row + 1][$this->player_col];
        $nextNextBlock = &$this->game[$this->player_row + 2][$this->player_col];
        if ($this->pushBox($nextBlock, $nextNextBlock)) {
            ++$this->player_row;
        }
    }

    public function moveLeft()
    {
        $nextBlock = &$this->game[$this->player_row][$this->player_col - 1];
        $nextNextBlock = &$this->game[$this->player_row][$this->player_col - 2];
        if ($this->pushBox($nextBlock, $nextNextBlock)) {
            --$this->player_col;
        }
    }

    public function moveRight()
    {
        $nextBlock = &$this->game[$this->player_row][$this->player_col + 1];
        $nextNextBlock = &$this->game[$this->player_row][$this->player_col + 2];
        if ($this->pushBox($nextBlock, $nextNextBlock)) {
            ++$this->player_col;
        }
    }

    public function blockAt(int $row, int $col): string
    {
        return $this->game[$row][$col] ?? self::BLOCK_EMPTY;
    }

    private function pushBox(string &$nextBlock, ?string &$nextNextBlock): bool
    {
        switch ($nextBlock) {
            // No box to push
            case self::BLOCK_EMPTY:
            case self::BLOCK_GOAL:
                return true;

            // There is a box? Checking next block
            case self::BLOCK_BOX:
            case self::BLOCK_BOX_ON_GOAL:
                switch ($nextNextBlock) {
                    // Box can be moved
                    case self::BLOCK_EMPTY:
                    case self::BLOCK_GOAL:
                        $nextBlock = $nextBlock === self::BLOCK_BOX ? self::BLOCK_EMPTY : self::BLOCK_GOAL;
                        $nextNextBlock = $nextNextBlock === self::BLOCK_EMPTY ? self::BLOCK_BOX : self::BLOCK_BOX_ON_GOAL;

                        return true;
                }
        }

        return false;
    }
}
