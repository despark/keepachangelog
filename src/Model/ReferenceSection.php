<?php
declare(strict_types=1);


namespace App\Model;


class ReferenceSection extends Section
{
    /**
     * ReferenceSection constructor.
     */
    public function __construct()
    {
        parent::__construct(0, '', new Line(''));
    }

}