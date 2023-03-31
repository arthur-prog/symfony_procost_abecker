<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\ProjectRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[Assert\Length(min:25, minMessage: 'La description doit faire au moins 25 caractères.')]
    #[ORM\Column(length: 1000)]
    private ?string $description = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $deliveryDate = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ProductionTime::class)]
    private Collection $productionTimes;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->productionTimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDeliveryDate(): ?DateTimeImmutable
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?DateTimeImmutable $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * @return Collection<int, ProductionTime>
     */
    public function getProductionTimes(): Collection
    {
        return $this->productionTimes;
    }

    public function addProductionTime(ProductionTime $productionTime): self
    {
        if (!$this->productionTimes->contains($productionTime)) {
            $this->productionTimes->add($productionTime);
            $productionTime->setProject($this);
        }

        return $this;
    }

    public function removeProductionTime(ProductionTime $productionTime): self
    {
        if ($this->productionTimes->removeElement($productionTime)) {
            // set the owning side to null (unless already changed)
            if ($productionTime->getProject() === $this) {
                $productionTime->setProject(null);
            }
        }

        return $this;
    }
}
