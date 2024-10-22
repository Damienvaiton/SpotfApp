<?php

namespace App\Factory;

use App\Entity\Artist;

class ArtistFactory
{
    public function createfromAPIArray(array $artists): array
    {
        $artist = [];
        foreach ($artists['items'] as $art) {
            array_push($artist, $this->createfromAPI($art));
        }
        dump($artist);
        return $artist;
    }

    public function createfromAPI(array $artist): Artist
    {
        if (empty($artist['images'])) {
            $artist['images'][0]['url'] = 'https://www.publicdomainpictures.net/pictures/280000/velka/not-found-image-15383864787lu.jpg';
        }


        return new Artist(
            $artist['name'],
            $artist['popularity'],
            $artist['uri'],
            $artist['followers']['total'],
            $artist['href'],
            $artist['id'],
            $artist['type'],
            $artist['images'][0]['url']
        );
    }
}