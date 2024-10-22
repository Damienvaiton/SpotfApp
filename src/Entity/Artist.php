<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $popularity;

    #[ORM\Column]
    private string $Uri;

    #[ORM\Column]
    private int $followers;

    #[ORM\Column]
    private string $href;

    #[ORM\Column]
    private string $idspotify;

    #[ORM\Column]
    private string $type;

    #[ORM\Column]
    private string $imagesUrl;

    private bool $isFavorite;

    public function __construct(string $name, string $popularity, string $Uri, int $followers, string $href, string $idspotify, string $type, string $imagesUrl)
    {
        $this->name = $name;
        $this->popularity = $popularity;
        $this->Uri = $Uri;
        $this->followers = $followers;
        $this->href = $href;
        $this->idspotify = $idspotify;
        $this->type = $type;
        $this->imagesUrl = $imagesUrl;
        $this->isFavorite = false;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImagesUrl(): string
    {
        return $this->imagesUrl;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getIdspotify(): string
    {
        return $this->idspotify;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return int
     */
    public function getFollowers(): int
    {
        return $this->followers;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->Uri;
    }

    /**
     * @return string
     */
    public function getPopularity(): string
    {
        return $this->popularity;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function IsFavorite(): bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): void
    {
        $this->isFavorite = $isFavorite;
    }
}
