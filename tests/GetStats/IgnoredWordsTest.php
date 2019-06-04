<?php declare(strict_types=1);

namespace app\tests\GetStats;

use app\commands\GetStats\IgnoredWords;
use PHPUnit\Framework\TestCase;

class IgnoredWordsTest extends TestCase
{
    /** @var string */
    private $lastIgnoredWordsPath;

    /** @var IgnoredWords */
    private $ignoredWords;

    protected function setUp(): void
    {
        $this->lastIgnoredWordsPath = sys_get_temp_dir() . '/' . uniqid() . '_words.txt';

        $this->ignoredWords = new IgnoredWords($this->lastIgnoredWordsPath);
    }

    public function testParseFromVacancyText(): void
    {
        $text = 'Some text with any words';
        $this->ignoredWords->parseFromVacancyText($text);

        $ignoredWordsProp = new \ReflectionProperty(IgnoredWords::class, 'ignoredWords');
        $ignoredWordsProp->setAccessible(true);
        $ignoredWordsPropVal = $ignoredWordsProp->getValue($this->ignoredWords);

        $this->assertIsArray($ignoredWordsPropVal);
        $this->assertCount(5, $ignoredWordsPropVal);
        $this->assertArrayHasKey('some', $ignoredWordsPropVal);
        $this->assertEquals('Some', $ignoredWordsPropVal[array_key_first($ignoredWordsPropVal)]);
        $this->assertArrayHasKey('words', $ignoredWordsPropVal);
        $this->assertEquals('words', $ignoredWordsPropVal[array_key_last($ignoredWordsPropVal)]);
    }

    public function testSave(): void
    {
        $ignoredWordsProp = new \ReflectionProperty(IgnoredWords::class, 'ignoredWords');
        $ignoredWordsProp->setAccessible(true);
        $ignoredWords = ['word1', 'word2', 'word3'];
        $ignoredWordsProp->setValue($this->ignoredWords, $ignoredWords);

        $this->ignoredWords->save();

        $this->assertFileExists($this->lastIgnoredWordsPath);
        $this->assertStringEqualsFile($this->lastIgnoredWordsPath, implode(PHP_EOL, $ignoredWords));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->lastIgnoredWordsPath)) {
            unlink($this->lastIgnoredWordsPath);
        }
    }
}