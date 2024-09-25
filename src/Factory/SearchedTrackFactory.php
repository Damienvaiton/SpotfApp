<?php

namespace App\Factory;

use App\Entity\SearchedTrack;

class SearchedTrackFactory
{
    public function createFromSpotifyApi(array $track): SearchedTrack
    {
        return new SearchedTrack(
            $track['name'],
            $track['artists'][0]['name'],
            $track['album']['name'],
            $track['id'],
            $track['album']['images'][0]['url']
        );
    }

    public function createFromSpotifyApiArray(array $tracks): array
    {
        $searchtrack = [];
        foreach ($tracks['tracks']['items'] as $track) {
            $searchtrack[] = $this->createFromSpotifyApi($track);
        }
        return $searchtrack;
    }
}