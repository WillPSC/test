<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     */
    private $password;

   

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, inversedBy="users")
     */
    private $shopping;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $order_id = [];

    public function __construct()
    {
        $this->Shopping = new ArrayCollection();
        $this->userOrder = new ArrayCollection();
        $this->shopping = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

  
    /**
     * @return Collection|Product[]
     */
    public function getShopping(): Collection
    {
        return $this->shopping;
    }

    public function addShopping(Product $shopping): self
    {
        if (!$this->shopping->contains($shopping)) {
            $this->shopping[] = $shopping;
           // $shopping->setUser($this);
        }

        return $this;
    }

    public function removeShopping(Product $shopping): self
    {
        if ($this->shopping->removeElement($shopping)) {
            // set the owning side to null (unless already changed)
            if ($shopping->getUsers() === $this) {
                $shopping->setUser(null);
            }
        }

        return $this;
    }

    public function getOrderId(): ?array
    {
        return $this->order_id;
    }

    public function setOrderId(?array $order_id): self
    {
        $this->order_id = $order_id;

        return $this;
    }


}
