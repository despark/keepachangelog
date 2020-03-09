<?php
declare(strict_types=1);


namespace App\Model;


class EmptyReferenceSection extends Section
{
    public function __construct(string $repositoryUrl)
    {
        $lines[] = new Line('[Unrelesed]: %s');
        parent::__construct(2, '', $lines);
    }
}