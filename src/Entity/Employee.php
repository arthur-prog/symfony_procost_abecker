<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[Assert\Email(message: 'L\'adresse mail {{ value }} n\'est pas valide.')]
    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\Column]
    private ?int $dailyCost = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $hiringDate = null;

    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Job $job = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: ProductionTime::class)]
    private Collection $productionTimes;

    public function __construct()
    {
        $this->productionTimes = new ArrayCollection();
    }

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getDailyCost(): ?string
    {
        return $this->dailyCost;
    }

    public function setDailyCost(string $dailyCost): self
    {
        $this->dailyCost = $dailyCost;

        return $this;
    }

    public function getHiringDate(): ?\DateTimeImmutable
    {
        return $this->hiringDate;
    }

    public function setHiringDate(\DateTimeImmutable $hiringDate): self
    {
        $this->hiringDate = $hiringDate;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

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
            $productionTime->setEmployee($this);
        }

        return $this;
    }

    public function removeProductionTime(ProductionTime $productionTime): self
    {
        if ($this->productionTimes->removeElement($productionTime)) {
            // set the owning side to null (unless already changed)
            if ($productionTime->getEmployee() === $this) {
                $productionTime->setEmployee(null);
            }
        }

        return $this;
    }
}
