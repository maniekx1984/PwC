<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KeywordRepository")
 */
class Keyword
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
    private $keyword;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Query", mappedBy="keyword")
     */
    private $queries;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="keywords")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    public function __construct()
    {
        $this->queries = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * @return Collection|Query[]
     */
    public function getQueries(): Collection
    {
        return $this->queries;
    }

    public function addQuery(Query $query): self
    {
        if (!$this->queries->contains($query)) {
            $this->queries[] = $query;
            $query->setKeyword($this);
        }

        return $this;
    }

    public function removeQuery(Query $query): self
    {
        if ($this->queries->contains($query)) {
            $this->queries->removeElement($query);
            // set the owning side to null (unless already changed)
            if ($query->getKeyword() === $this) {
                $query->setKeyword(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }
}
