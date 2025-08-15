<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Repository;

use Djdmg\TransparentPixelBundle\Entity\Pixel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PixelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Pixel::class); }
    public function findOneByName(string $name): ?Pixel { return $this->findOneBy(['name' => $name]); }
    public function findActiveByToken(string $token): ?Pixel { return $this->findOneBy(['token' => $token, 'active' => true]); }
}
