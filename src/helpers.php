<?php

if (!function_exists('array_merge_recursive_distinct')) {
    function array_merge_recursive_distinct(array &$source, array &$incoming): array
    {
        $merged = $source;

        foreach ($incoming as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}

if (!function_exists('array_export_pretty')) {
    function array_export_pretty(mixed $value, string $indent = ''): string {
        $output = preg_replace("/^([ ]*)(.*)/m", $indent.'$1$1$2', var_export($value, true));
        $lines = preg_split("/\r\n|\n|\r/", $output);
        $replaced = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $lines);
        return join(PHP_EOL, array_filter(["["] + $replaced));
    }
}
