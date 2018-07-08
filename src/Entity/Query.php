<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QueryRepository")
 */
class Query
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $queryTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $responseCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    public function getId()
    {
        return $this->id;
    }

    public function getQueryTime(): ?\DateTimeInterface
    {
        return $this->queryTime;
    }

    public function setQueryTime(\DateTimeInterface $queryTime): self
    {
        $this->queryTime = $queryTime;

        return $this;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
