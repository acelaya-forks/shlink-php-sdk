<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

enum TagsMode: string
{
    case ANY = 'any';
    case ALL = 'all';
}
