<?php
declare(strict_types=1);


namespace App\Model;


/**
 * Class Section
 * @package App\Model
 */
class Section
{
    const UNRELEASED = '[Unreleased]';
    /**
     * @var \App\Model\Line[]
     */
    private $lines;
    /**
     * @var int
     */
    private $level;
    /**
     * @var string
     */
    private $heading;
    /**
     * @var \App\Model\Section[]
     */
    private $subSections = [];
    /**
     * @var bool
     */
    private $unreleased;

    /**
     * Section constructor.
     *
     * @param int $level
     * @param string $heading
     * @param \App\Model\Line|null ...$lines
     */
    public function __construct(int $level, string $heading, ?Line ...$lines)
    {
        $this->lines = $lines;
        $this->level = $level;
        $this->heading = trim($heading);
    }

    /**
     * @param \App\Model\Section $section
     */
    public function addVersion(Section $section)
    {
        $this->subSections[] = $section;
    }

    /**
     * @param \App\Model\Line $line
     */
    public function addLine(Line $line)
    {
        $this->lines[] = $line;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // Third level will not be rendered if no content
        if ($this->getLevel() === 3 && $this->isEmpty()) {
            return '';
        }

        $h = str_repeat('#', $this->level);
        $heading = trim($h.' '.$this->heading);

        $lines = array_merge(
            array_map(function (Line $line): string {
                return $line->__toString();
            }, $this->lines),
            array_filter(array_map(function (Section $section): string {
                return trim($section->__toString()).PHP_EOL;
            }, $this->subSections)),
        );
        // Add the heading
        if($heading){
            array_unshift($lines, $heading);
        }
        return implode(PHP_EOL, $lines);
    }

    /**
     * @return \App\Model\Line[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return bool
     */
    public function isUnreleased(): bool
    {
        if (!isset($this->unreleased)) {
            $this->unreleased = $this->heading === self::UNRELEASED;
        }

        return $this->unreleased;
    }

    /**
     * Check to see if section contain any content
     * @return bool
     */
    public function isEmpty(): bool
    {
        $lines = array_filter(array_map(function (Line $line) {
            return !$line->isEmpty();
        }, $this->lines));

        return empty($lines) && empty($this->subSections);
    }

    /**
     * @return \App\Model\Section[]
     */
    public function getVersions(): array
    {
        return $this->subSections;
    }

    /**
     * @param \App\Model\Section $section
     */
    public function prependVersion(Section $section)
    {
        array_unshift($this->subSections, $section);
    }

    /**
     * @param string $heading
     */
    public function setHeading(string $heading): void
    {
        $this->heading = $heading;

        unset($this->unreleased);
    }

}