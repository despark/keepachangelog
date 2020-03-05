<?php
declare(strict_types=1);


namespace App\Exceptions\Changelog;


use App\Exceptions\Changelog;
use Throwable;

class MissingUnreleasedSection extends Changelog
{
    /**
     * MissingUnreleasedSection constructor.
     *
     * @param string $template
     * @param \Throwable|null $previous
     */
    public function __construct(string $template, Throwable $previous = null)
    {
        $message = sprintf('Missing unrelesed section in changelog. Searched for %s', $template);
        parent::__construct($message, 0, $previous);
    }
}