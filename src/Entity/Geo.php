<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GeoRepository;

/**
 * Geo
 *
 * @ORM\Table(name="Geo")
 * @ORM\Entity(repositoryClass=GeoRepository::class)
 */
class Geo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=10, nullable=false)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lng", type="string", length=10, nullable=false)
     */
    private $lng;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat($lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng($lng): self
    {
        $this->lng = $lng;

        return $this;
    }


}
