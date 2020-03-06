<?php
declare(strict_types=1);


namespace App\Service;


use App\Exceptions\Changelog\MissingUnreleasedSection;
use App\Model\Changelog;
use App\Model\Section;
use App\Model\UndefinedSection;
use DateTime;

/**
 * Class ChangelogService
 * @package App\Service
 */
class ChangelogService
{
    /**
     * @param string $content
     *
     * @param $version
     * @param \DateTime $date
     *
     * @return \App\Model\Changelog
     * @throws \App\Exceptions\Changelog\MissingUnreleasedSection
     */
    public function release(string $content, $version, DateTime $date = null): Changelog
    {
        $changelog = new Changelog($content);
        // Get unreleased section
        $unreleasedSection = $changelog->getUnreleased();
        if ($unreleasedSection instanceof UndefinedSection) {
            throw new MissingUnreleasedSection(Section::UNRELEASED);
        }

        $unreleasedSection->setHeading(sprintf('[%s] - %s',
            $version,
            $date->format('Y-m-d')
        ));

        $changelog->addUnreleased();

        return $changelog;
    }

}