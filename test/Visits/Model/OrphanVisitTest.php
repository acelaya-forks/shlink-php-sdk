<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Visits\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisit;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisitType;
use Shlinkio\Shlink\SDK\Visits\Model\VisitLocation;

class OrphanVisitTest extends TestCase
{
    #[Test, DataProvider('providePayloads')]
    public function properObjectIsCreatedFromArray(
        array $payload,
        string $expectedReferer,
        DateTimeInterface $expectedDate,
        string $expectedUserAgent,
        bool $expectedPotentialBot,
        VisitLocation|null $expectedLocation,
        string $expectedVisitedUrl,
        OrphanVisitType $expectedType,
    ): void {
        $visit = OrphanVisit::fromArray($payload);

        self::assertEquals($expectedReferer, $visit->referer());
        self::assertEquals($expectedDate, $visit->date());
        self::assertEquals($expectedUserAgent, $visit->userAgent());
        self::assertEquals($expectedPotentialBot, $visit->potentialBot());
        self::assertEquals($expectedLocation, $visit->location());
        self::assertEquals($expectedVisitedUrl, $visit->visitedUrl());
        self::assertEquals($expectedType, $visit->type());
    }

    public static function providePayloads(): iterable
    {
        $now = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');
        $formattedDate = $now->format(DateTimeInterface::ATOM); // @phpstan-ignore-line

        yield 'defaults' => [[
            'date' => $formattedDate,
        ], '', $now, '', false, null, '', OrphanVisitType::REGULAR_NOT_FOUND];
        yield 'all data' => [
            [
                'referer' => 'referer',
                'date' => $formattedDate,
                'userAgent' => 'userAgent',
                'potentialBot' => true,
                'visitLocation' => [],
                'visitedUrl' => 'https://s.test/foo/bar',
                'type' => OrphanVisitType::BASE_URL->value,
            ],
            'referer',
            $now,
            'userAgent',
            true,
            VisitLocation::fromArray([]),
            'https://s.test/foo/bar',
            OrphanVisitType::BASE_URL,
        ];
    }
}
