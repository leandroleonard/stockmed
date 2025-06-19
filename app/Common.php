<?php

function dd(mixed $data): void
{
    echo "<pre>";
    exit(var_dump($data));
}

function resumeText(string $text, int $len = 20): string
{
    if(strlen($text) > $len) return substr($text, 0, $len);

    return $text;
}