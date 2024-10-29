<?php

declare(strict_types=1);

namespace App\Utils;

use RuntimeException;

final readonly class FileUtil
{
    public static function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }
    }
}
