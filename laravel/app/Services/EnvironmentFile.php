<?php

namespace App\Services;

use RuntimeException;

class EnvironmentFile
{
    public function set(array $values): void
    {
        $path = app()->environmentFilePath();
        $contents = is_file($path) ? file_get_contents($path) : '';

        if ($contents === false) {
            throw new RuntimeException('Unable to read the environment file.');
        }

        foreach ($values as $key => $value) {
            $line = $key.'='.$this->quote((string) $value);
            $pattern = '/^'.preg_quote($key, '/').'=.*/m';

            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $line, $contents, 1);
            } else {
                $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
            }
        }

        if (file_put_contents($path, $contents, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write the environment file. Check file permissions.');
        }
    }

    private function quote(string $value): string
    {
        return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
    }
}
