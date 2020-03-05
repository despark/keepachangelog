<?php
declare(strict_types=1);


namespace App\Service;


use App\Exceptions\Changelog\MissingUnreleasedSection;
use DateTime;

/**
 * Class ChangelogService
 * @package App\Service
 */
class ChangelogService
{

    /**
     * @var array
     */
    private $sections = [
        'Added',
        'Changed',
        'Deprecated',
        'Removed',
        'Fixed',
        'Security',
    ];

    /**
     * @var string
     */
    private $unreleasedTemplate = '## [Unreleased]';

    /**
     * @param string $content
     *
     * @param $version
     * @param \DateTime $date
     *
     * @return string
     * @throws \App\Exceptions\Changelog\MissingUnreleasedSection
     */
    public function release(string $content, $version, DateTime $date = null): string
    {
        // Get unreleased section
        $matches = [];
        $hasUnreleased = preg_match('/(?<='.preg_quote($this->unreleasedTemplate).')(.*?)(##\s?\[)/mis', $content,
            $matches);
        if (!$hasUnreleased) {
            throw new MissingUnreleasedSection($this->unreleasedTemplate);
        }
        if (is_null($date)) {
            $date = new DateTime();
        }
        if (isset($matches[1])) {
            // Use every line and loop on it
            $sections = $this->extractSections(preg_split('/\r\n|\r|\n/', trim($matches[1])));
            $flatSections = [];
            foreach ($sections as $section) {
                $flatSections[] = implode(PHP_EOL, $section).PHP_EOL;
            }
            $release = PHP_EOL.PHP_EOL.sprintf(
                    '## [%s] - %s',
                    $version,
                    $date->format('Y-m-d')).
                PHP_EOL.
                implode(PHP_EOL, $flatSections).PHP_EOL;
            $content = str_replace($matches[1], $release, $content);
        }

        return $content;
    }

    /**
     * @param $lines
     *
     * @return array
     */
    private function extractSections($lines): array
    {
        $s = 0;
        $sections = [];
        $skip = true;
        // we collect every section lines
        foreach ($lines as $line) {
            if (strpos($line, '###') === 0) {
                $skip = true;
                // check if section is legit
                foreach ($this->sections as $allowedSection) {
                    if (stristr($line, $allowedSection) !== false) {
                        // if we don't have correct section we just skip loops
                        $skip = false;
                        $s++;
                        break;
                    }
                }
            }
            if (!$skip) {
                $sections[$s][] = $line;
            }
        }
        $sections = array_filter($sections);

        foreach ($sections as $num => &$lines) {
            $lines = array_filter($lines);
            if (count($lines) <= 1) {
                unset($sections[$num]);
            }
        }
        return $sections;
    }

}