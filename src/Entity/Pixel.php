<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Djdmg\TransparentPixelBundle\Repository\PixelRepository;

#[ORM\Entity(repositoryClass: PixelRepository::class)]
#[ORM\Table(name: 'tp_pixel')]
class Pixel
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(length: 64, unique: true)]
    private string $token;

    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function __construct(string $name, ?array $metadata = null)
    {
        $this->name = $name;
        $this->token = bin2hex(random_bytes(32));
        $this->createdAt = new \DateTimeImmutable();
        $this->metadata = $metadata;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getToken(): string { return $this->token; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): void { $this->active = $active; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getMetadata(): ?array { return $this->metadata; }
    public function setMetadata(?array $metadata): void { $this->metadata = $metadata; }
}
