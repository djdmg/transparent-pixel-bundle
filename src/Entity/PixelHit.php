<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\TrackingPixel\Repository\PixelHitRepository;

#[ORM\Entity(repositoryClass: PixelHitRepository::class)]
#[ORM\Table(name: 'tp_pixel_hit', indexes: [
    new ORM\Index(columns: ['created_at']),
    new ORM\Index(columns: ['pixel_id']),
    new ORM\Index(columns: ['ip']),
])]
class PixelHit
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Pixel::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Pixel $pixel;

    #[ORM\Column(length: 64)]
    private string $token;

    #[ORM\Column(length: 45)]
    private string $ip = '0.0.0.0';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $referer = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $method = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $os = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $browser = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $device = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isMobile = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $isBot = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $headers = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $query = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $cookies = null;

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    public function __construct(Pixel $pixel)
    {
        $this->pixel = $pixel;
        $this->token = $pixel->getToken();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getPixel(): Pixel { return $this->pixel; }
    public function setPixel(Pixel $pixel): void { $this->pixel = $pixel; }
    public function getToken(): string { return $this->token; }
    public function getIp(): string { return $this->ip; }
    public function setIp(string $ip): void { $this->ip = $ip; }
    public function getUserAgent(): ?string { return $this->userAgent; }
    public function setUserAgent(?string $ua): void { $this->userAgent = $ua; }
    public function getReferer(): ?string { return $this->referer; }
    public function setReferer(?string $referer): void { $this->referer = $referer; }
    public function getMethod(): ?string { return $this->method; }
    public function setMethod(?string $method): void { $this->method = $method; }
    public function getOs(): ?string { return $this->os; }
    public function setOs(?string $os): void { $this->os = $os; }
    public function getBrowser(): ?string { return $this->browser; }
    public function setBrowser(?string $browser): void { $this->browser = $browser; }
    public function getDevice(): ?string { return $this->device; }
    public function setDevice(?string $device): void { $this->device = $device; }
    public function isMobile(): bool { return $this->isMobile; }
    public function setIsMobile(bool $isMobile): void { $this->isMobile = $isMobile; }
    public function isBot(): bool { return $this->isBot; }
    public function setIsBot(bool $isBot): void { $this->isBot = $isBot; }
    public function getHeaders(): ?array { return $this->headers; }
    public function setHeaders(?array $headers): void { $this->headers = $headers; }
    public function getQuery(): ?array { return $this->query; }
    public function setQuery(?array $query): void { $this->query = $query; }
    public function getCookies(): ?array { return $this->cookies; }
    public function setCookies(?array $cookies): void { $this->cookies = $cookies; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
