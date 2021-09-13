<?php

namespace App\Service;

class EmbedlyUrlHelper
{
    public function embed($url): string
    {
        if (\preg_match("/(?::\/\/w{0,3}.*)(youtube|youtu)(?:.(?:com|be)\/).*(.{11})/", $url, $match)) {
            return $url = "https://www.youtube.com/embed/$match[2]";
        } elseif (\preg_match("/(?:.*)(dailymotion\.com)(?:.*)(\/x[\w\-\_]{5,6})/", $url, $match)) {
            return $url = "https://www.dailymotion.com/embed/video$match[2]";
        } elseif (\preg_match("/(?:.*)(vimeo\.com)(?:.*)(\/[\w\-\_]{8,9})/", $url, $match)) {
            return $url = "https://player.vimeo.com/video$match[2]";
        };
        return $url;
    }
}
