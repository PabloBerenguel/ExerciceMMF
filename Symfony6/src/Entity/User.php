<?php

namespace App\Entity;

use App\Controller\UserController;
use App\Enum\Role;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]

/* TODO: Due to time limitation the Api Platform could not be implemented
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => 'user:list']],
        "create" => [
            'controller' => UserController::class,
            'method' => 'POST',
            'path' => '/users',
            "route_name"=>"UserController.create",
        ],
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => 'user:item']],
    ],
    order: ['id' => 'ASC'],
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
)]
*/

class User
{
    CONST PASSWORD_LENGTH = 6;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    // #[Groups(['user:list', 'user:item'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    // #[Groups(['user:list', 'user:item', 'user:write'])]
    private string $first_name;

    #[ORM\Column(type: 'string', length: 255)]
    // #[Groups(['user:list', 'user:item', 'user:write'])]
    private string $last_name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    // #[Groups(['user:list', 'user:item', 'user:write'])]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    // #[Groups(['user:list', 'user:item', 'user:write'])]
    private string $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    // #[Groups(['user:list', 'user:item', 'user:write'])]
    private ?string $profile_picture;

    #[ORM\Column(type: 'string', length: 255, options: ["default" => Role::USER])]
    // #[Groups(['user:list', 'user:item'])]
    private string $role;

    #[ORM\OneToOne(mappedBy: "user", targetEntity: "AuthToken")]
    // #[Groups(['user:list', 'user:item', 'user:write'])]
    private AuthToken $authToken;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AuthToken::class)]
    private $accessToken;

    public function __construct()
    {
        $this->accessToken = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getAuthToken(): ?AuthToken
    {
        dd($this);
        return $this->authToken;
    }

    /**
     * @return Collection<int, AuthToken>
     */
    public function getAccessToken(): Collection
    {
        return $this->accessToken;
    }

    public function addAccessToken(AuthToken $accessToken): self
    {
        if (!$this->accessToken->contains($accessToken)) {
            $this->accessToken[] = $accessToken;
            $accessToken->setUser($this);
        }

        return $this;
    }

    public function removeAccessToken(AuthToken $accessToken): self
    {
        if ($this->accessToken->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getUser() === $this) {
                $accessToken->setUser(null);
            }
        }

        return $this;
    }
}
