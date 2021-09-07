<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('preg_replace', [$this, 'pregReplace']),
        ];
    }

    public function pregReplace($subject, $pattern, $replacement)
    {
        if (!isset($subject)) {
            return null;
        } else {
            return preg_replace($pattern, $replacement, $subject);
        }
    }
}
