<?php declare(strict_types=1);

namespace app\tests;

use app\commands\GetStats\Stats;
use PHPUnit\Framework\TestCase;

class StatsTest extends TestCase
{
    /** @var array */
    private $config;

    /** @var Stats */
    private $stats;

    protected function setUp(): void
    {
        $this->config = require __DIR__ . '/../configs/common.php';
        $this->config['paths']['stats_json'] = sys_get_temp_dir() . '/' . uniqid() . '_stats.json';

        $this->stats = new Stats($this->config['tech_patterns'], $this->config['paths']['stats_json']);
    }

    public function testParseFromVacancyText(): void
    {
        $text = 'Some text symfony yii2 Laravel 5.1';
        $this->stats->parseFromVacancyText($text, 'some_source');

        $statsProp = new \ReflectionProperty(Stats::class, 'stats');
        $statsProp->setAccessible(true);
        $parsedStats = $statsProp->getValue($this->stats);

        $this->assertIsArray($parsedStats);
        $this->assertIsArray($parsedStats['some_source'][date('n')]['PHP фреймворки']);
        $this->assertCount(3, $parsedStats['some_source'][date('n')]['PHP фреймворки']);
        $this->assertEquals(1, $parsedStats['some_source'][date('n')]['PHP фреймворки']['Symfony']);
    }

    public function testSort(): void
    {
        $statsProp = new \ReflectionProperty(Stats::class, 'stats');
        $statsProp->setAccessible(true);
        $currMonth = date('n');
        $statsProp->setValue($this->stats, [
            'some_source' => [
                $currMonth => [
                    'PHP CMS' => [
                        'Drupal' => 5,
                        'Wordpress' => 50,
                        'Bitrix' => 40,
                    ],
                    'PHP фреймворки' => [
                        'Symfony' => 25,
                        'Yii' => 2,
                        'Laravel' => 15,
                    ],
                ]
            ]
        ]);

        $this->stats->sort('some_source');

        $sortedStats = $statsProp->getValue($this->stats);
        $this->assertEquals('PHP фреймворки', array_key_first($sortedStats['some_source'][$currMonth]));
        $this->assertEquals('PHP CMS', array_key_last($sortedStats['some_source'][$currMonth]));
        $this->assertEquals('Wordpress', array_key_first($sortedStats['some_source'][$currMonth]['PHP CMS']));
        $this->assertEquals('Drupal', array_key_last($sortedStats['some_source'][$currMonth]['PHP CMS']));
    }

    public function testSave(): void
    {
        $statsProp = new \ReflectionProperty(Stats::class, 'stats');
        $statsProp->setAccessible(true);
        $statsProp->setValue($this->stats, [
            'some_source' => [
                1 => [
                    'some_category' => [
                        'some_tech' => 25
                    ]
                ]
            ]
        ]);

        $this->stats->save();

        $this->assertFileExists($this->config['paths']['stats_json']);
        $this->assertJson(file_get_contents($this->config['paths']['stats_json']));

        unlink($this->config['paths']['stats_json']);
    }
}
