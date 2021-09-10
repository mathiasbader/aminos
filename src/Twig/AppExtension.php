<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    function getFilters(): array
    {
        return [
            new TwigFilter('lower_digits', [$this, 'lowerDigits']),
        ];
    }

    function lowerDigits($data): string
    {
        return preg_replace('/[0-9]/', '<span style="vertical-align: sub; font-size: smaller;">${0}</span>', $data);
    }
}
