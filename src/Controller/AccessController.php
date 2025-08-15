<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Controller;

use App\TrackingPixel\Repository\PixelHitRepository;
use App\TrackingPixel\Repository\PixelRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AccessController
{
    public function __construct(
        private PixelRepository $pixels,
        private PixelHitRepository $hits,
    ) {}

    #[Route('/_tp/{token}/access', name: 'transparent_pixel_access', methods: ['GET'])]
    public function list(string $token, Request $request): JsonResponse
    {
        $limit = (int) max(1, min(2000, (int) $request->query->get('limit', 200)));
        $pixel = $this->pixels->findActiveByToken($token);
        if (!$pixel) return new JsonResponse(['error' => 'Pixel not found or inactive'], 404);

        return new JsonResponse([
            'pixel'   => ['id' => $pixel->getId(), 'name' => $pixel->getName(), 'token' => $pixel->getToken()],
            'count'   => $limit,
            'results' => $this->hits->getAccessDetails($pixel, $limit),
        ]);
    }
}
