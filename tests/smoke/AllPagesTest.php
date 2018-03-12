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

    protected function setUp()
    {
        $this->client = $this->getClient();
    }

    /**
     * @group        functional
     * @dataProvider urlProvider
     */
    public function testAllRoutes(string $method, string $url, string $status_code): void
    {
        $response = $this->client->request($method, $url, [
            'exceptions' => false
        ]);

        $this->assertEquals($status_code, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody()->getContents());
    }

    /**
     * @return mixed[]
     */
    public function urlProvider(): array
    {
        return [
            ['GET', '/',        StatusCode::STATUS_OK]
        ];
    }
}
