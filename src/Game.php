<?php

namespace Phpokoban;

class Game
{
    public const BLOCK_WALL = 'W';
    public const BLOCK_BOX = 'B';
    public const BLOCK_GOAL = '*';
    public const BLOCK_BOX_OK = 'G';
    public const BLOCK_EMPTY = ' ';
    public const PLAYER_POS = 'P';

    /** @var string[][] */
    private $game = [];
    /** @var int */
    private $height = 0;
    /** @var int */
    private $width = 0;
    /** @var Position */
    private $player;

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
                        $this->player = new Position(count($row), $i);
                        $block = self::BLOCK_EMPTY;
                        break;
                    case self::BLOCK_EMPTY:
                    case self::BLOCK_GOAL:
                    case self::BLOCK_WALL:
                    case self::BLOCK_BOX:
                    case self::BLOCK_BOX_OK:
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
        return $this->player;
    }

    public function moveUp()
    {
        if ($this->player->row > 0) {
            --$this->player->row;
        }
    }

    public function moveDown()
    {
        if ($this->player->row < $this->height - 1) {
            ++$this->player->row;
        }
    }

    public function moveLeft()
    {
        if ($this->player->col > 0) {
            --$this->player->col;
        }
    }

    public function moveRight()
    {
        if ($this->player->col < $this->width - 1) {
            ++$this->player->col;
        }
    }

    public function blockAt(int $row, int $col): string
    {
        return $this->game[$row][$col] ?? self::BLOCK_EMPTY;
    }
}
