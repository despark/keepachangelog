<?php
declare(strict_types=1);


namespace App\Model;


class Changelog
{
    /**
     * @var string
     */
    private $newLineRegex = '/\r\n|\r|\n/';
    /**
     * @var string
     */
    private $markdown;
    /**
     * @var \App\Model\ReferenceSection
     */
    private $referenceSection;
    /**
     * @var bool
     */
    private $loaded;
    /**
     * @var \App\Model\Section
     */
    private $changelog;

    public function __construct(string $markdown)
    {
        $this->markdown = $markdown;
        $this->load();
    }

    /**
     * @return \App\Model\Section
     */
    public function getChangelog(): Section
    {
        return $this->changelog;
    }

    /**
     * Adds unreleased version to the changelog
     */
    public function addUnreleased()
    {
        if ($this->getUnreleased() instanceof UndefinedSection) {
            $this->changelog->prependVersion(new EmptyUnreleasedSection());
        }
    }

    /**
     * @return \App\Model\Section
     */
    public function getUnreleased(): Section
    {
        foreach ($this->changelog->getVersions() as $section) {
            if ($section->isUnreleased()) {
                return $section;
            }
        }

        return new UndefinedSection();
    }

    public function getReference(): ReferenceSection
    {
        foreach ($this->changelog->getVersions() as $version) {
            if ($version instanceof ReferenceSection) {
                return $version;
            }
        }
        return new ReferenceSection();
    }

    /**
     * Loads all sections from the changelog.
     * Currently simple markdown parsing
     */
    protected function load()
    {
        $this->referenceSection = new ReferenceSection();
        $sections = [];
        $references = false;
        foreach (preg_split($this->newLineRegex, $this->markdown) as $line) {
            $cleanLine = trim($line);
            // if it start with # we have a section
            if (strpos($cleanLine, '#') === 0) {
                // Dump any created sections
                if (isset($section)) {
                    $sections[] = $section;
                }
                // Find the level
                $level = substr_count($cleanLine, '#', 0, 4);
                // The heading
                $heading = substr($cleanLine, $level + 1);
                // And create new section
                $section = new Section($level, $heading);
                continue;
            }
            // We also need to close when we hit the reference section
            if (preg_match('/^\[.*\]:/', $cleanLine) && isset($section)) {
                $sections[] = $section;
                unset($section);
                $references = true;
            }
            // Build reference section
            if ($references) {
                $this->referenceSection->addLine(new Line($line));
                continue;
            }

            $section->addLine(new Line($line));
        }

        $this->changelog = $this->structureSections(...$sections);

        // Add references as a zero section
        $this->changelog->addVersion($this->referenceSection);

        $this->loaded = true;
    }

    /**
     * Structures all the sections into levels of objects
     *
     * @param \App\Model\Section ...$sections
     *
     * @return \App\Model\Section
     */
    private function structureSections(Section ...$sections): Section
    {
        // First section is the parent
        $parent = reset($sections);
        //        $next = [];
        foreach ($sections as $idx => $section) {
            // If we hit an equal level section we add the rest of the array for structuring for this parent
            if ($section !== $parent && $section->getLevel() === $parent->getLevel()) {
                $this->structureSections(... array_slice($sections, $idx));
                break;
            }
            // First children are added as subsections and added for recursion
            if (($section->getLevel() - 1) === $parent->getLevel()) {
                $parent->addVersion($section);
                $next[] = $section;
            }
            // Same is true for deeper levels
            if (($section->getLevel() - 1) > $parent->getLevel()) {
                $next[] = $section;
            }
        }
        if (!empty($next)) {
            $this->structureSections(...$next);
        }

        return $parent;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return trim($this->getChangelog()->__toString());
    }

}