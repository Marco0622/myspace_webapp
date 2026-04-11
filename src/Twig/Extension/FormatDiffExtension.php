<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\FormatDiffRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FormatDiffExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('date_diff', [FormatDiffRuntime::class, 'dateDiff']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [FormatDiffRuntime::class, 'doSomething']),
        ];
    }
}
