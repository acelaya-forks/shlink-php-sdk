<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsListOrderField;

class TagsFilterTest extends TestCase
{
    /**
     * @param callable(): TagsFilter $buildFilter
     */
    #[Test, DataProvider('provideOrderings')]
    public function requiresPaginationBasedOnTheOrderingFields(callable $buildFilter, bool $shouldPaginate): void
    {
        $tagsFilter = $buildFilter();
        self::assertEquals($shouldPaginate, $tagsFilter->shouldPaginateRequest());
    }

    public static function provideOrderings(): iterable
    {
        yield 'no order' => [TagsFilter::create(...), true];
        yield 'tag ASC' => [static fn () => TagsFilter::create()->orderingAscBy(TagsListOrderField::TAG), true];
        yield 'tag DESC' => [static fn () => TagsFilter::create()->orderingDescBy(TagsListOrderField::TAG), true];
        yield 'shortUrlsCount ASC' => [
            static fn () => TagsFilter::create()->orderingAscBy(TagsListOrderField::SHORT_URLS_COUNT),
            false,
        ];
        yield 'shortUrlsCount DESC' => [
            static fn () => TagsFilter::create()->orderingDescBy(TagsListOrderField::SHORT_URLS_COUNT),
            false,
        ];
        yield 'visitsCount ASC' => [
            static fn () => TagsFilter::create()->orderingAscBy(TagsListOrderField::VISITS_COUNT),
            false,
        ];
        yield 'visitsCount DESC' => [
            static fn () => TagsFilter::create()->orderingDescBy(TagsListOrderField::VISITS_COUNT),
            false,
        ];
        yield 'override towards paginable' => [
            static fn () => TagsFilter::create()
                ->orderingAscBy(TagsListOrderField::SHORT_URLS_COUNT)
                ->orderingDescBy(TagsListOrderField::TAG),
            true,
        ];
        yield 'override towards non-paginable' => [
            static fn () => TagsFilter::create()
                ->orderingDescBy(TagsListOrderField::TAG)
                ->orderingAscBy(TagsListOrderField::VISITS_COUNT),
            false,
        ];
    }
}
