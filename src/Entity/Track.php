<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Track
{
   #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;


   #[ORM\Column(type: 'integer')]
    private int $discNumber;

   #[ORM\Column(type: 'integer')]
    private int $durationMs;

    #[ORM\Column(type: 'boolean')]
    private bool $explicit;

    #[ORM\Column(type: 'string')]
    private string $isrc;


    #[ORM\Column(type: 'string')]
    private string $albumname;


    #[ORM\Column(type: 'string')]
    private string $artists;

    #[ORM\Column(type: 'string')]
    private string $spotifyUrl;

    #[ORM\Column(type: 'string')]
    private string $href;

    #[ORM\Column(type: 'string')]
    private string $idspotify;

    #[ORM\Column(type: 'boolean')]
    private bool $isLocal;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $popularity;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $previewUrl;

    #[ORM\Column(type: 'integer')]
    private int $trackNumber;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'string')]
    private string $uri;

    #[ORM\Column(type: 'string')]
    private ?string $pictureLink;

    private ?bool $isFavorite = null;



    public function __construct(
        int $discNumber,
        int $durationMs,
        bool $explicit,
        string $isrc,
        string $albumname,
        string $spotifyUrl,
        string $artists,
        string $href,
        string $idspotify,
        bool $isLocal,
        string $name,
        int $popularity,
        ?string $previewUrl,
        int $trackNumber,
        string $type,
        string $uri,
        ?string $pictureLink
    ) {
        $this->discNumber = $discNumber;
        $this->durationMs = $durationMs;
        $this->explicit = $explicit;
        $this->isrc = $isrc;
        $this->spotifyUrl = $spotifyUrl;
        $this->albumname = $albumname;
        $this->artists = $artists;
        $this->href = $href;
        $this->idspotify = $idspotify;
        $this->isLocal = $isLocal;
        $this->name = $name;
        $this->popularity = $popularity;
        $this->previewUrl = $previewUrl;
        $this->trackNumber = $trackNumber;
        $this->type = $type;
        $this->uri = $uri;
        $this->pictureLink = $pictureLink;
        $this->isFavorite = false;
    }

    public function getDiscNumber(): int
    {
        return $this->discNumber;
    }

    public function getDurationMs(): int
    {
        return $this->durationMs;
    }

    public function isExplicit(): bool
    {
        return $this->explicit;
    }

    public function getIsrc(): string
    {
        return $this->isrc;
    }

    public function getArtists(): string
    {
        return $this->artists;
    }

    public function getSpotifyUrl(): string
    {
        return $this->spotifyUrl;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getIdSpotify(): string
    {
        return $this->idspotify;
    }

    public function isLocal(): bool
    {
        return $this->isLocal;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }

    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPictureLink(): string
    {
        return $this->pictureLink;
    }

    public function getAlbumName(): string
    {
        return $this->albumname;
    }

    public function isFavorite(): ?bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): static
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

}
