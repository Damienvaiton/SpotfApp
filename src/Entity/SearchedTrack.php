<?php

namespace App\Entity;

class SearchedTrack
{
    private string $name;
    private string $artistname;
    private string $albumname;
    private string $id;
    private string $pictureLink;

    public function __construct(
        string $name,
        string $artistname,
        string $albumname,
        string $id,
        string $pictureLink
    ) {
        $this->name = $name;
        $this->artistname = $artistname;
        $this->albumname = $albumname;
        $this->id = $id;
        $this->pictureLink = $pictureLink;
    }

    // Getters for all properties
    public function getName(): string
    {
        return $this->name;
    }

    public function getArtistname(): string
    {
        return $this->artistname;
    }

    public function getAlbumname(): string
    {
        return $this->albumname;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPictureLink(): string
    {
        return $this->pictureLink;
    }

    public function getSpotifyUrl(): string
    {
        return 'https://open.spotify.com/track/' . $this->id;
    }

    public function getHref(): string
    {
        return 'https://api.spotify.com/v1/tracks/' . $this->id;
    }







}