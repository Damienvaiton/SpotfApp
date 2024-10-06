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
            $track['album']['name'],
            $track['external_urls']['spotify'],
            $this->feats($track),
            $track['href'],
            $track['id'],
            $track['is_local'],
            $track['name'],
            $track['popularity'],
            $track['preview_url'] ?? null,
            $track['track_number'],
            $track['type'],
            $track['uri'],
            $track['album']['images'][0]['url']
        );
    }

    public function feats(array $track) : string
    {
        $feats = '';
        foreach ($track['artists'] as $artist) {
            $feats .= $artist['name'] . ', ';
        }
        return substr($feats, 0, -2);
    }


    public function createfromAPIArray(array $tracks) : array
    {
        $track = [];
        foreach ($tracks['items'] as $tr) {
            array_push($track, $this->createfromAPI($tr));
        }
        return $track;
    }
}