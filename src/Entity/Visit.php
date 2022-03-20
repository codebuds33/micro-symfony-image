<?php

namespace App\Entity;

use App\Repository\VisitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitRepository::class)]
class Visit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $client;

    #[ORM\Column(type: 'string', length: 255)]
    private $server;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $route = '/';

    #[ORM\Column(type: 'json', nullable: true)]
    private array|null $data;

    public function __construct()
    {
        $this->setCreatedAt();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getServer(): ?string
    {
        return $this->server;
    }

    public function setServer(string $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setCreatedAt(): void
    {
        if($this->createdAt !== null) {
            throw new \RuntimeException('CreatedAt date has already been set');
        }

        $this->createdAt = new \DateTimeImmutable();
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getData(): array|null
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s : %s | %s : %s | %s : %s | %s : %s',
        'Created', $this->createdAt->format('Y-m-d H:m:i'),
                'Server', $this->server,
                'Client', $this->client,
                'Data', json_encode($this->data),
        );
    }
}
