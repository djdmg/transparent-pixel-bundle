<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Twig;

use Djdmg\TransparentPixelBundle\Entity\Pixel;
use Djdmg\TransparentPixelBundle\Service\PixelManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PixelExtension extends AbstractExtension
{
    public function __construct(private PixelManager $manager) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('transparent_pixel_tag', [$this, 'tag'], ['is_safe' => ['html']]),
            new TwigFunction('transparent_pixel_url', [$this, 'url']),
        ];
    }

    public function tag(Pixel $pixel, array $extraQuery = []): string
    {
        $url = $this->url($pixel, $extraQuery);
        return sprintf('<img src="%s" width="1" height="1" style="display:none" alt="" loading="eager">', htmlspecialchars($url, ENT_QUOTES));
    }

    public function url(Pixel $pixel, array $extraQuery = []): string
    {
        return $this->manager->getPixelUrl($pixel, $extraQuery);
    }
}
