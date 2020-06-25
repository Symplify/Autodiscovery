<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Yaml;

use Symfony\Component\Yaml\Yaml;
use Symplify\Autodiscovery\HttpKernel\AutodiscoveryKernel;
use Symplify\Autodiscovery\Yaml\ExplicitToAutodiscoveryConverter;
use Symplify\EasyTesting\Fixture\FixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ExplicitToAutodiscoveryConverterTest extends AbstractKernelTestCase
{
    /**
     * @var ExplicitToAutodiscoveryConverter
     */
    private $explicitToAutodiscoveryConverter;

    protected function setUp(): void
    {
        $this->bootKernel(AutodiscoveryKernel::class);

        $this->explicitToAutodiscoveryConverter = static::$container->get(ExplicitToAutodiscoveryConverter::class);
    }

    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixture/short_tags.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/vendor.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/singly_implemented_interfaces.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/singly_implemented_interfaces_excluded.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/first.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/tags_with_values.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/shopsys.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/elasticr.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/untouch.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/existing_autodiscovery.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/blog_post_votruba.yaml', 1);
        $this->doTestFile(__DIR__ . '/Fixture/exclude.yaml', 4);
    }

    private function doTestFile(string $file, int $nestingLevel): void
    {
        $fixtureSplitter = new FixtureSplitter();
        $fileInfo = new SmartFileInfo($file);
        [$inputYamlContent, $expectedYamlContent] = $fixtureSplitter->splitFileInfoToInputAndExpected($fileInfo);

        $originalYaml = Yaml::parse($inputYamlContent);
        $expectedYaml = Yaml::parse($expectedYamlContent);

        $this->assertSame(
            $expectedYaml,
            $this->explicitToAutodiscoveryConverter->convert($originalYaml, $file, $nestingLevel, ''),
            'Caused by ' . $file
        );
    }
}
