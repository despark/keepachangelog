<?php
declare(strict_types=1);


namespace App\Model;

/**
 * Class EmptyUnreleasedSection
 * @package App\Model
 */
class EmptyUnreleasedSection extends Section
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
     * EmptyUnreleasedSection constructor.
     */
    public function __construct()
    {
        parent::__construct(2, self::UNRELEASED);

        foreach ($this->sections as $section) {
            $this->addVersion(new Section(3, $section, new Line('')));
        }
    }

    /**
     * @return bool
     */
    public function isUnreleased(): bool
    {
        return true;
    }
}