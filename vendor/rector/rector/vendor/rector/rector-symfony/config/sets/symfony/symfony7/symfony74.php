<?php

declare (strict_types=1);
namespace RectorPrefix202507;

use Rector\Config\RectorConfig;
// @see https://github.com/symfony/symfony/blob/7.4/UPGRADE-7.4.md
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->import(__DIR__ . '/symfony74/symfony74-console.php');
};
