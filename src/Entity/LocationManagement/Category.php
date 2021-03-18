<?php

namespace App\Entity\LocationManagement;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LocationManagement\Location;
use App\Entity\LocationManagement\Color;
use App\Repository\LocationManagement\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tag;

    /**
     * @ORM\OneToOne(targetEntity="Color")
     * @ORM\JoinColumn(name="color_id", referencedColumnName="id")
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="category")
     */
    private $locations;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct() {
        $this->locations = new ArrayCollection();
        $this->active = true;
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

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function getLocations()
    {
        return $this->locations;
    }

    public function addLocation($location)
    {
        if(!$this->locations->contains($location)){
            $this->locations[] = $location;
            $locations->setCategory($this);
        }
    }

    public function removeLocation($location)
    {
        if($this->locations->contains($location)){
            $this->locations->removeElement($location);
            if($location->getCategory() === $this){
                $location->setCategory(null);
            }
        }
    }

    public function isActive(){
        return $this->active;
    }

    public function setActive($active){
        $this->active = $active;
    }
}
