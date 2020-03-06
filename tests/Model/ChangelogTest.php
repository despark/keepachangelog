<?php
declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\Changelog;
use PHPUnit\Framework\TestCase;

class ChangelogTest extends TestCase
{


    public function test_get_sections_on_first_release_changelog()
    {
        $changelogString = <<<CHANGELOG
# Section H1
Long title on multiple
lines

- Something
- Something other
## [Unreleased]
### Updated
- we have first subsection in H3
- another one
### Added
- Something added

 [Unreleased]: https://example.com
CHANGELOG;


        $changelog = new Changelog($changelogString);
        $markdown = $changelog->__toString();

        $expectedChangelog = <<<EXPECTED_CHANGELOG
# Section H1
Long title on multiple
lines

- Something
- Something other
## [Unreleased]
### Updated
- we have first subsection in H3
- another one

### Added
- Something added

[Unreleased]: https://example.com
EXPECTED_CHANGELOG;
        $this->assertEquals($expectedChangelog, $markdown);
    }
}
