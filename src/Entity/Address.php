<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Post(securityPostDenormalize: 'is_granted("ADRESS_CREATE", object)'),
        new Get(
            security: 'is_granted("ADDRESS_VIEW_ITEM", object)',
            normalizationContext: ['groups' => ['address:read', 'address:item:get']]
        ),
        new Put(security: 'is_granted("ADDRESS_EDIT", object)'),
        new Patch(security: 'is_granted("ADDRESS_EDIT", object)'),
        new Delete(security: 'is_granted("ADDRESS_DELETE", object)'),
    ],
    normalizationContext: ['groups' => ['address:read']],
)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'address:read'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'address:read'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 20)]
    #[Groups(['user:read', 'address:read'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'address:read'])]
    private ?string $street = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'address:read'])]
    private ?string $city = null;

    #[ORM\Column(length: 10)]
    #[Groups(['user:read', 'address:read'])]
    private ?string $postcode = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['address:read'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

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
}
