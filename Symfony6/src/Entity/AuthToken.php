<?php

namespace App\Entity;

use App\Repository\AuthTokenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthTokenRepository::class)]
class AuthToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\Column(type: 'string', length: 255)]
    private string $token;

    #[ORM\Column(type: 'date')]
    private $valid_until;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'accessToken')]
    private $user;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'accessToken')]
    private $refreshToken;

    #[ORM\OneToMany(mappedBy: 'refreshToken', targetEntity: self::class, orphanRemoval: true)]
    private $accessToken;

    public function __construct()
    {
        $this->accessToken = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->valid_until;
    }

    public function setValidUntil(\DateTimeInterface $valid_until): self
    {
        $this->valid_until = $valid_until;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRefreshToken(): ?self
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?self $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAccessToken(): Collection
    {
        return $this->accessToken;
    }

    public function addAccessToken(self $accessToken): self
    {
        if (!$this->accessToken->contains($accessToken)) {
            $this->accessToken[] = $accessToken;
            $accessToken->setRefreshToken($this);
        }

        return $this;
    }

    public function removeAccessToken(self $accessToken): self
    {
        if ($this->accessToken->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getRefreshToken() === $this) {
                $accessToken->setRefreshToken(null);
            }
        }

        return $this;
    }
}
