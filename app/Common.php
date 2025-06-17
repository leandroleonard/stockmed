<?php

function dd(mixed $data): void
{
    echo "<pre>";
    exit(var_dump($data));
}