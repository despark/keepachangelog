<?php
declare(strict_types=1);


namespace App\Model;


class Line
{
    /**
     * @var string
     */
    private $line;

    public function __construct(string $line)
    {
        $this->line = $line;
    }

    public function isEmpty()
    {
        return empty($this->line);
    }

    public function __toString()
    {
        return $this->line;
    }
}