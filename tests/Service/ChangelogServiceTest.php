<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ChangelogService;
use DateTime;
use PHPUnit\Framework\TestCase;

class ChangelogServiceTest extends TestCase
{

    /**
     * @var string
     */
    private $changelog;
    /**
     * @var \App\Service\ChangelogService
     */
    private $changelogService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->changelog = file_get_contents(realpath(__DIR__.'/../resources/CHANGELOG.md'));

        $this->changelogService = new ChangelogService();
    }

    public function testRelease()
    {
        $releasedChangelog = $this->changelogService->release($this->changelog, '1.2.2', DateTime::createFromFormat('Y-m-d', '2020-03-21'));
        $expected = file_get_contents(realpath(__DIR__.'/../resources/expected.md'));
        $this->assertEquals($expected, (string)$releasedChangelog);
    }
}
