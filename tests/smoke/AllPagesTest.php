<?php

declare(strict_types=1);

namespace JasperTest\Smoke;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class AllPagesTest extends TestCase
{
    use SmokeContainerTrait;

    /** @var Client */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->getClient();
    }

    /**
     * @group        smoke
     * @dataProvider urlProvider
     */
    public function testAllRoutes(string $method, string $url, int $status_code): void
    {
        $response = $this->client->request($method, $url, [
            'exceptions' => false
        ]);

        self::assertEquals($status_code, $response->getStatusCode());
        self::assertNotEmpty($response->getBody()->getContents());
    }

    /**
     * @return mixed[]
     */
    public function urlProvider(): array
    {
        return [
            ['GET', '/',                StatusCode::STATUS_OK],
            ['GET', '/data/northwind.json',  StatusCode::STATUS_OK],
            ['GET', '/data/northwind.xml',   StatusCode::STATUS_OK]
        ];
    }
}
