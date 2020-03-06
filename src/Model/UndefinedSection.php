<?php
declare(strict_types=1);


namespace App\Model;


class UndefinedSection extends Section
{
    public function __construct()
    {
        parent::__construct(0, '');
    }
}