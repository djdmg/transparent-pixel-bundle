<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Repository;

use App\TrackingPixel\Entity\Pixel;
use App\TrackingPixel\Entity\PixelHit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PixelHitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, PixelHit::class); }

    public function getAccessDetails(?Pixel $pixel = null, int $limit = 1000): array
    {
        $qb = $this->createQueryBuilder('h')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults($limit);

        if ($pixel) { $qb->andWhere('h.pixel = :p')->setParameter('p', $pixel); }

        /** @var PixelHit[] $rows */
        $rows = $qb->getQuery()->getResult();

        return array_map(static function (PixelHit $h): array {
            return [
                'at'       => $h->getCreatedAt()->format(DATE_ATOM),
                'ip'       => $h->getIp(),
                'os'       => $h->getOs(),
                'browser'  => $h->getBrowser(),
                'device'   => $h->getDevice(),
                'isMobile' => $h->isMobile(),
                'isBot'    => $h->isBot(),
                'method'   => $h->getMethod(),
                'referer'  => $h->getReferer(),
                'headers'  => $h->getHeaders() ?? [],
                'query'    => $h->getQuery() ?? [],
                'cookies'  => $h->getCookies() ?? [],
                'ua'       => $h->getUserAgent(),
                'token'    => $h->getToken(),
                'pixel'    => ['id' => $h->getPixel()->getId(), 'name' => $h->getPixel()->getName()],
            ];
        }, $rows);
    }
}
