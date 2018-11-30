<?php

namespace Phpokoban;

class Position
{
    /** @var int */
    public $row = 0;
    /** @var int */
    public $col = 0;

    public function __construct(int $col, int $row)
    {
        $this->col = $col;
        $this->row = $row;
    }
}
