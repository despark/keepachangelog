<?php
declare(strict_types=1);


namespace App\Exceptions\Changelog;


use App\Exceptions\Changelog;
use Throwable;

class BadSectionStructure extends Changelog
{
    public function __construct($message = "Bad changelog sections structure", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}