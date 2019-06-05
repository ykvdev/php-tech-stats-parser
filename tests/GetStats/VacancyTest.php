<?php declare(strict_types=1);

namespace app\tests\GetStats;

use app\commands\GetStats\Vacancy;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class VacancyTest extends TestCase
{
    public function testGetUrlsListFromPage(): void
    {
        $vacancy = new Vacancy();

        $guzzleHttpClientProp = new \ReflectionProperty(Vacancy::class, 'guzzleHttpClient');
        $guzzleHttpClientProp->setAccessible(true);
        $guzzleHttpClientProp->setValue($vacancy, new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Some title</title>
            </head>
            <body>
                <div class="main-container">
                    <div class="vacancy-container">
                        <a href="http://some-site.com/some-vacancy-1.html">Some Vacancy 1</a>
                    </div>
                    <div class="vacancy-container">
                        <a href="http://some-site.com/some-vacancy-2.html">Some Vacancy 2</a>
                    </div>
                    <div class="vacancy-container">
                        <a href="http://some-site.com/some-vacancy-3.html">Some Vacancy 3</a>
                    </div>
                </div>
            </body>
            </html>
            ')
        ]))]));

        $vacancyUrls = $vacancy->getUrlsListFromPage('http://some-site.com/',
            '.main-container .vacancy-container a');

        $this->assertIsArray($vacancyUrls);
        $this->assertCount(3, $vacancyUrls);
        $this->assertEquals('http://some-site.com/some-vacancy-1.html', $vacancyUrls[0]);
    }

    public function testGetTextByUrl(): void
    {
        $vacancy = new Vacancy();

        $guzzleHttpClientProp = new \ReflectionProperty(Vacancy::class, 'guzzleHttpClient');
        $guzzleHttpClientProp->setAccessible(true);
        $guzzleHttpClientProp->setValue($vacancy, new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Some title</title>
            </head>
            <body>
                <div class="main-container">
                    <div class="vacancy-container">
                        <h1>Some vacancy title</h1>
                        <p>Some vacancy text...</p>
                    </div>
                </div>
            </body>
            </html>
            ')
        ]))]));

        $vacancyText = $vacancy->getTextByUrl('http://some-site.com/some-vacancy-1.html',
            '.main-container .vacancy-container');

        $this->assertIsString($vacancyText);
        $this->assertRegExp('/Some vacancy title.*Some vacancy text/s', $vacancyText);
    }
}