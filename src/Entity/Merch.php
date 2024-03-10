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

    #[ORM\OneToMany(targetEntity: File::class, mappedBy: "merch")]
    private Collection $photos;

    #[ORM\JoinTable(name: 'merchs_sizes')]
    #[ORM\JoinColumn(name: 'merch_id', referencedColumnName: 'id', onDelete: "CASCADE")]
    #[ORM\InverseJoinColumn(name: 'size_id', referencedColumnName: 'id', onDelete: "CASCADE")]
    #[ORM\ManyToMany(targetEntity: Size::class)]
    private Collection $sizes;

    public function __construct()
    {
        $this->sizes = new ArrayCollection();
        $this->photos = new ArrayCollection();
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

    /**
     * @return Collection<int, File>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function addPhoto(File $file): self
    {
        if (!$this->photos->contains($file)) {
            $this->photos[] = $file;
            $file->setMerch($this);
        }

        return $this;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function removePhoto(File $file): self
    {
        if ($this->photos->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getMerch() === $this) {
                $file->setMerch(null);
            }
        }

        return $this;
    }

    /**
     * @return array|File[]
     */
    public function getOrderedImages(): array
    {
        $images = $this->photos->getValues();

        usort($images, function (File $left, File $right) {
            return $left->getPosition() <=> $right->getPosition();
        });

        return array_values($images);
    }

    public function getOrderedImagesPaths(): array
    {
        return array_map(function (File $file) {
            return $file->getFileName();
        }, $this->getOrderedImages());
    }

    public function getPreview(): ?string
    {
        return $this->getOrderedImagesPaths()[0] ?? null;
    }

    public function getMaxPosition(): int
    {
        $orders = array_map(function (File $file) {
            return $file->getPosition();
        }, $this->photos->getValues());

        if (empty($orders)) {
            return 1;
        }

        return max($orders) + 1;
    }

    public function reposition(File $file, $way): void
    {
        $orderedImages = $this->getOrderedImages();
        $maxOrder = count($orderedImages);
        foreach ($orderedImages as $index => $image) {
            if ($image->getId() === $file->getId()) {
                $index += $way;
            }
            $index = $index === $maxOrder ? $index - 1 : $index;
            $index = $index === -1 ? 0 : $index;
            $image->setPosition($index);
        }

        foreach ($orderedImages as $index => $image) {
            if ($index > 0 && $orderedImages[$index-1]->getPosition() === $image->getPosition()) {
                if ($way === 1) {
                    $image->setPosition($image->getPosition() - 1);
                }
                if ($way === -1) {
                    $orderedImages[$index-1]->setPosition($image->getPosition() + 1);
                }
            }
        }
    }
}
