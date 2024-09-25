<?php

namespace App\Factory;

use App\Entity\Track;

class TrackFactory
{
    public function createfromAPI(array $track) : Track
    {
        return new Track(
            $track['disc_number'],
            $track['duration_ms'],
            $track['explicit'],
            $track['external_ids']['isrc'],
            $track['external_urls']['spotify'],
            $track['href'],
            $track['id'],
            $track['is_local'],
            $track['name'],
            $track['popularity'],
            $track['preview_url'],
            $track['track_number'],
            $track['type'],
            $track['uri'],
            $track['album']['images'][0]['url']
        );
    }


    public function createfromAPIArray(array $tracks) : array
    {
        $track = [];
        foreach ($tracks['items'] as $track) {
            $track[] = $this->createfromAPI($track);
        }
        return $track;
    }
}