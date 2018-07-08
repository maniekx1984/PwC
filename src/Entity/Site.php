<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Keyword", mappedBy="site")
     */
    private $keywords;

    public function __construct()
    {
        $this->keywords = new ArrayCollection();
    }

    public function getId()
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

    /**
     * @return Collection|Keyword[]
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(Keyword $keyword): self
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords[] = $keyword;
            $keyword->setSite($this);
        }

        return $this;
    }

    public function removeKeyword(Keyword $keyword): self
    {
        if ($this->keywords->contains($keyword)) {
            $this->keywords->removeElement($keyword);
            // set the owning side to null (unless already changed)
            if ($keyword->getSite() === $this) {
                $keyword->setSite(null);
            }
        }

        return $this;
    }
}
