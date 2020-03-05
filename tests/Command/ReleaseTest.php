<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\Release;
use App\Service\ChangelogService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ReleaseTest
 * @package App\Tests\Command
 */
class ReleaseTest extends TestCase
{

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    private $commandTester;
    /**
     * @var \App\Command\Release
     */
    private $command;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->command = $this->createCommandInstance();
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * Test release
     */
    public function testRelease()
    {
        $src = realpath(__DIR__.'/../resources/CHANGELOG.md');
        $dest = __DIR__.'/../resources/CHANGELOG_TEST.md';
        $out = __DIR__.'/../resources/CHANGELOG_OUT.md';
        copy($src, $dest);
        $this->commandTester->execute([
            'version' => '1.2.2',
            '--file' => $dest,
            '--out' => $out,
            '--date' => '2020-03-21',
        ]);

        $result = file_get_contents($out);
        $expected = file_get_contents(realpath(__DIR__.'/../resources/expected.md'));

        @unlink($out);
        @unlink($dest);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return \App\Command\Release
     */
    private function createCommandInstance(): Release
    {
        $instance = new Release();
        $instance->setFilesystem(new Filesystem());
        $instance->setChangelogService(new ChangelogService());

        return $instance;
    }

}
