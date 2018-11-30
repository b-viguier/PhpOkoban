<?php

namespace Phpokoban;

class Position
{
    /** @var int */
    private $row = 0;
    /** @var int */
    private $col = 0;

    public function __construct(int $col, int $row)
    {
        $this->col = $col;
        $this->row = $row;
    }

    public function row(): int
    {
        return $this->row;
    }

    public function col(): int
    {
        return $this->col;
    }
}
