<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlListOrderField;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;

class ShortUrlsFilterTest extends TestCase
{
    #[Test, DataProvider('providePayloads')]
    public function payloadIsBuiltAsExpected(callable $createFilter, array $expected): void
    {
        /** @var ShortUrlsFilter $filter */
        $filter = $createFilter();

        self::assertEquals($expected, $filter->toArray());
    }

    public static function providePayloads(): iterable
    {
        $date = new DateTimeImmutable();

        yield [ShortUrlsFilter::create(...), []];
        yield [
            static fn () => ShortUrlsFilter::create()
                ->since($date)
                ->until($date),
            ['startDate' => $formatted = $date->format(DateTimeInterface::ATOM), 'endDate' => $formatted],
        ];
        yield [
            static fn () => ShortUrlsFilter::create()
                ->containingSomeTags('foo', 'bar')
                ->searchingBy('searching'),
            ['tags' => ['foo', 'bar'], 'tagsMode' => 'any', 'searchTerm' => 'searching'],
        ];
        yield [
            static fn () => ShortUrlsFilter::create()->containingAllTags('foo', 'bar'),
            ['tags' => ['foo', 'bar'], 'tagsMode' => 'all'],
        ];
        yield [
            static fn () => ShortUrlsFilter::create()->orderingAscBy(ShortUrlListOrderField::VISITS),
            ['orderBy' => 'visits-ASC'],
        ];
        yield [
            static fn () => ShortUrlsFilter::create()->orderingDescBy(ShortUrlListOrderField::LONG_URL),
            ['orderBy' => 'longUrl-DESC'],
        ];
        yield [
            static fn () => ShortUrlsFilter::create()->excludingMaxVisitsReached()->excludingPastValidUntil(),
            ['excludeMaxVisitsReached' => 'true', 'excludePastValidUntil' => 'true'],
        ];
        yield [static fn () => ShortUrlsFilter::create()->forDomain('s.test'), ['domain' => 's.test']];
        yield [
            static fn () => ShortUrlsFilter::create()->notContainingSomeTags('foo', 'bar', 'baz'),
            ['excludeTags' => ['foo', 'bar', 'baz'], 'excludeTagsMode' => 'any'],
        ];
        yield [
            static fn () => ShortUrlsFilter::create()->notContainingAnyTags('foo', 'bar', 'baz'),
            ['excludeTags' => ['foo', 'bar', 'baz'], 'excludeTagsMode' => 'all'],
        ];
        yield [static fn () => ShortUrlsFilter::create()->createdWithApiKey('foo'), ['apiKeyName' => 'foo']];
    }
}
