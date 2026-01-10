<?php

declare (strict_types=1);
namespace ComfortSmtpScoped\Doctrine\Inflector;

interface WordInflector
{
    public function inflect(string $word): string;
}
