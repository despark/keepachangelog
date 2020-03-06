<?php /** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);


namespace App\Command;


use App\Service\ChangelogService;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Release extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'release';

    /**
     * @var string
     */
    private $defaultChangelogName = 'CHANGELOG.md';
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;
    /**
     * @var \App\Service\ChangelogService
     */
    private $changelogService;

    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setDescription('Releases new changelog version at desired location');
        $this->addArgument('version', InputArgument::REQUIRED, 'Version to release');
        $this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, "Path to changelog file");
        $this->addOption('out', 'o', InputOption::VALUE_OPTIONAL, "Path to released changelog");
        $this->addOption('date', 'd', InputOption::VALUE_OPTIONAL, "Release date");
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     * @throws \App\Exceptions\Changelog\MissingUnreleasedSection
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ver = $input->getArgument('version');
        $changelogPath = $input->getOption('file') ?? getcwd().DIRECTORY_SEPARATOR.$this->defaultChangelogName;
        $strDate = $input->getOption('date');
        $dateTime = $strDate ? DateTime::createFromFormat('U', (string)strtotime($strDate)) : new DateTime();

        if (!$this->filesystem->exists($changelogPath)) {
            $output->writeln(sprintf('Changelog missing at %s', realpath($changelogPath)));
            return 1;
        }

        $outputChangelog = $input->getOption('out') ?? $changelogPath;
        $changelogContent = file_get_contents($changelogPath);
        $releasedChangelog = $this->changelogService->release($changelogContent, $ver, $dateTime);
        $this->filesystem->dumpFile($outputChangelog, (string)$releasedChangelog);
        $output->writeln(sprintf('Changelog released at %s', realpath($outputChangelog)));

        return 0;

    }

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     *
     * @required
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param \App\Service\ChangelogService $changelogService
     *
     * @required
     */
    public function setChangelogService(ChangelogService $changelogService)
    {
        $this->changelogService = $changelogService;
    }
}