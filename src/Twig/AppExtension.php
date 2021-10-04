<?php

namespace App\Twig;

use App\Entity\User;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('preg_replace', [$this, 'pregReplace']),
            new TwigFilter('gravatar', [$this, 'gravatar'])
        ];
    }

    public function pregReplace(string $subject, string $pattern, string $replacement)
    {
        if (!isset($subject)) {
            return null;
        }
        return preg_replace($pattern, $replacement, $subject);
    }

    /**
     * Get either a Gravatar URL
     * @param User $subject
     * @param int $size - Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @return string
     */
    public function gravatar(User $subject, int $size)
    {
        // Default imageset to use [ 404 | mp | identicon | monsterid | wavatar | retro | robohash | blank
        // https://fr.gravatar.com/site/implement/images/
        $defaultImg = 'identicon';

        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($subject->getEmail())));
        $url .= "?s=$size&d=$defaultImg&r=g";
        return $url;
    }
}
