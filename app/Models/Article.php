<?php

declare(strict_types=1);

namespace App\Models;

class Article
{
    private string $type;
    private string $setup;
    private string $punchline;

    public function __construct(string $type, string $setup, string $punchline)
    {
        $this->type = $type;
        $this->setup = $setup;
        $this->punchline = $punchline;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSetup(): string
    {
        return $this->setup;
    }

    public function getPunchline(): string
    {
        return $this->punchline;
    }
}