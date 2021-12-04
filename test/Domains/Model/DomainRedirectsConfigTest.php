<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Domains\Model;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectProps;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;

class DomainRedirectsConfigTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideConfigs
     */
    public function payloadIsBuiltAsExpected(callable $createConfig, array $expectedPayload): void
    {
        /** @var DomainRedirectsConfig $config */
        $config = $createConfig();
        self::assertEquals($expectedPayload, $config->jsonSerialize());
    }

    public function provideConfigs(): iterable
    {
        yield [fn () => DomainRedirectsConfig::forDomain('foo.com'), ['domain' => 'foo.com']];
        yield [
            fn () => DomainRedirectsConfig::forDomain('foo.com')
                ->withRegularNotFoundRedirect('somewhere.com'),
            [
                'domain' => 'foo.com',
                DomainRedirectProps::REGULAR_NOT_FOUND => 'somewhere.com',
            ],
        ];
        yield [
            fn () => DomainRedirectsConfig::forDomain('bar.com')
                ->withRegularNotFoundRedirect('foo.com')
                ->removingBaseUrlRedirect(),
            [
                'domain' => 'bar.com',
                DomainRedirectProps::REGULAR_NOT_FOUND => 'foo.com',
                DomainRedirectProps::BASE_URL => null,
            ],
        ];
        yield [
            fn () => DomainRedirectsConfig::forDomain('bar.com')
                ->withRegularNotFoundRedirect('foo.net')
                ->withInvalidShortUrlRedirect('something.com')
                ->removingBaseUrlRedirect(),
            [
                'domain' => 'bar.com',
                DomainRedirectProps::REGULAR_NOT_FOUND => 'foo.net',
                DomainRedirectProps::INVALID_SHORT_URL => 'something.com',
                DomainRedirectProps::BASE_URL => null,
            ],
        ];
        yield [
            fn () => DomainRedirectsConfig::forDomain('baz.com')
                ->removingBaseUrlRedirect()
                ->removingRegularNotFoundRedirect()
                ->removingInvalidShortUrlRedirect(),
            [
                'domain' => 'baz.com',
                DomainRedirectProps::REGULAR_NOT_FOUND => null,
                DomainRedirectProps::BASE_URL => null,
                DomainRedirectProps::INVALID_SHORT_URL => null,
            ],
        ];
        yield [
            fn () => DomainRedirectsConfig::forDomain('foobarbaz.com')
                ->withRegularNotFoundRedirect('foo.net')
                ->withInvalidShortUrlRedirect('something.com')
                ->withBaseUrlRedirect('base-redirect.com'),
            [
                'domain' => 'foobarbaz.com',
                DomainRedirectProps::REGULAR_NOT_FOUND => 'foo.net',
                DomainRedirectProps::INVALID_SHORT_URL => 'something.com',
                DomainRedirectProps::BASE_URL => 'base-redirect.com',
            ],
        ];
    }
}
