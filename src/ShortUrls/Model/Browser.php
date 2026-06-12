<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

enum Browser: string
{
    case CHROME = 'chrome';
    case FIREFOX = 'firefox';
    case EDGE = 'edge';
    case SAFARI = 'safari';
    case OPERA = 'opera';
    case ANDROID_BROWSER = 'android_browser';
}
