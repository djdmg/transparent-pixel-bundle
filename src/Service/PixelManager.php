<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Service;

use App\TrackingPixel\Entity\Pixel;
use App\TrackingPixel\Repository\PixelHitRepository;
use App\TrackingPixel\Repository\PixelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PixelManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private PixelRepository $pixels,
        private PixelHitRepository $hits,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function ensurePixel(string $name, ?array $metadata = null): Pixel
    {
        if ($p = $this->pixels->findOneByName($name)) return $p;
        $p = new Pixel($name, $metadata);
        $this->em->persist($p);
        $this->em->flush();
        return $p;
    }

    public function getPixelUrl(Pixel $pixel, array $extraQuery = []): string
    {
        $url = $this->urlGenerator->generate('transparent_pixel_hit', ['token' => $pixel->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        if ($extraQuery) $url .= (str_contains($url, '?') ? '&' : '?').http_build_query($extraQuery);
        return $url;
    }

    public function getAccessDetails(?Pixel $pixel = null, int $limit = 1000): array
    {
        return $this->hits->getAccessDetails($pixel, $limit);
    }
}
