<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Tests\Unit;

use Limenet\LaravelElasticaBridge\Logging\SentryBreadcrumbLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use Sentry\Breadcrumb;
use Sentry\ClientBuilder;
use Sentry\SentrySdk;

class SentryBreadcrumbLoggerTest extends TestCase
{
    /**
     * @var list<Breadcrumb>
     */
    private array $breadcrumbs = [];

    protected function setUp(): void
    {
        parent::setUp();

        SentrySdk::init()->bindClient(ClientBuilder::create([
            'before_breadcrumb' => function (Breadcrumb $breadcrumb): Breadcrumb {
                $this->breadcrumbs[] = $breadcrumb;

                return $breadcrumb;
            },
        ])->getClient());
    }

    public function test_logs_every_level_by_default(): void
    {
        $logger = new SentryBreadcrumbLogger;

        $logger->debug('Body');
        $logger->info('Request');

        $this->assertSame(['Body', 'Request'], $this->messages());
    }

    public function test_drops_messages_less_severe_than_the_minimum_level(): void
    {
        $logger = new SentryBreadcrumbLogger(LogLevel::INFO);

        $logger->debug('Body');
        $logger->info('Request');
        $logger->error('Retry');

        $this->assertSame(['Request', 'Retry'], $this->messages());
    }

    public function test_rejects_an_unknown_minimum_level(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SentryBreadcrumbLogger('verbose');
    }

    /**
     * @return list<string>
     */
    private function messages(): array
    {
        return array_map(fn (Breadcrumb $breadcrumb): string => (string) $breadcrumb->getMessage(), $this->breadcrumbs);
    }
}
