<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;


/**
 * @ApiResource
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
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
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firt_name;

    /**
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="user")
     */
    private $jobs;


    public function __construct()
    {

        $this->jobs =  new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getFirtName()
    {
        return $this->firt_name;
    }

    /**
     * @param mixed $firt_name
     */
    public function setFirtName($firt_name): void
    {
        $this->firt_name = $firt_name;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return Collection
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJobs(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setUser($this);
        }

        return $this;
    }

    public function removeJobs(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getUser() === $this) {
                $job->setUser(null);
            }
        }

        return $this;
    }

}
