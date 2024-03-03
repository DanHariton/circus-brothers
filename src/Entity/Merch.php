<?php

namespace App\Entity;

use App\Repository\MerchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: MerchRepository::class)]
class Merch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?bool $active = null;

    /**
     * @var Collection<int, Size>
     */
    #[ORM\JoinTable(name: 'merchs_sizes')]
    #[ORM\JoinColumn(name: 'merch_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'size_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Size::class)]
    private Collection $sizes;

    public function __construct()
    {
        $this->sizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Size>
     */
    public function getSizes(): Collection
    {
        return $this->sizes;
    }

    /**
     * @param Size $size
     * @return $this
     */
    public function addContact(Size $size): self
    {
        if (!$this->sizes->contains($size)) {
            $this->sizes[] = $size;
        }

        return $this;
    }

    /**
     * @param Size $size
     * @return $this
     */
    public function removeContact(Size $size): self
    {
        $this->sizes->removeElement($size);

        return $this;
    }
}
