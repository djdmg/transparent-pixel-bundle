<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Controller;

use Djdmg\TransparentPixelBundle\Entity\PixelHit;
use Djdmg\TransparentPixelBundle\Repository\PixelRepository;
use Djdmg\TransparentPixelBundle\Service\UserAgentParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PixelController
{
    private const GIF_1x1 = 'R0lGODlhAQABAPAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';

    public function __construct(
        private PixelRepository $pixels,
        private EntityManagerInterface $em,
        private UserAgentParser $uaParser,
    ) {}

    #[Route('/_tp/{token}', name: 'transparent_pixel_hit', methods: ['GET'])]
    public function __invoke(string $token, Request $request): Response
    {
        $response = new Response(base64_decode(self::GIF_1x1));
        $response->headers->set('Content-Type', 'image/gif');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        $pixel = $this->pixels->findActiveByToken($token);
        if (!$pixel) return $response;

        $ip = $request->getClientIp() ?? '0.0.0.0';
        $ua = $request->headers->get('User-Agent');
        $parsed = $this->uaParser->parse($ua);


        $hit = new PixelHit($pixel);
        $hit->setIp($ip);
        $hit->setUserAgent($ua);
        $hit->setReferer($request->headers->get('Referer'));
        $hit->setMethod($request->getMethod());
        $hit->setOs($parsed['os']);
        $hit->setBrowser($parsed['browser']);
        $hit->setDevice($parsed['device']);
        $hit->setIsMobile($parsed['isMobile']);
        $hit->setIsBot($parsed['isBot']);
        $hit->setHeaders($request->headers->all());
        $hit->setQuery($request->query->all());
        $hit->setCookies($request->cookies->all());

        $this->em->persist($hit);
        $this->em->flush();

        return $response;
    }
}
