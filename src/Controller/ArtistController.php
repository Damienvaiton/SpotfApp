<?php

namespace App\Controller;

use App\Factory\ArtistFactory;
use App\service\AuthSpotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArtistController extends AbstractController
{
    private string $token;

    public function __construct(
        private readonly AuthSpotifyService $authSpotifyService,

    )
    {
        $this->token = $this->authSpotifyService->auth();
    }

    public function GetArtist(): array
    {
        $client = HttpClient::create();
        $id = "06HL4z0CvFAxyc27GXpf02";
        $response = $client->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        return $response->toArray();
    }

    #[Route('/artist', name: 'app_artist')]
    public function index(): Response
    {
        $artistFactory = new ArtistFactory();
        $artist = $artistFactory->createfromAPI($this->GetArtist());


        return $this->render('artist/index.html.twig', [
            'controller_name' => 'ArtistController',
            'artist' => $artist
        ]);
    }

    #[Route('/searchedArtist', name: 'app_searchedArtist')]
    public function searchedArtist(Request $request,Security $security): Response
    {
        if ($security->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }
        $artists = [];
        $form = $this->createFormBuilder()->add(
            'search',
            TextType::class
        )->setMethod('GET')->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $artistsresult = $this->GetArtistFromName($data['search']);

            $artistsfactory = new ArtistFactory();
            $artists = $artistsfactory->createfromAPIArray($artistsresult['artists']);

        }
        return $this->render('artist/searchedArtist.html.twig', [
            'form' => $form->createView(),
            'artists' => $artists,
        ]);
    }

    #[Route('/artist/{id}/details', name: 'app_artist_details')]
    public function artistDetails(string $id): Response
    {
        $artistFactory = new ArtistFactory();
        $artist = $artistFactory->createfromAPI($this->GetArtistFromId($id));
        $albums = $this->GetArtistAlbums($id);
        $recommendations = $this->GetArtistRecommendations($id);

        dump($recommendations);


        return $this->render('artist/artistDetails.html.twig', [
            'artist' => $artist,
            'albums' => $albums,
            'recommendations' => $recommendations,

        ]);
    }



    public function GetArtistFromName (string $name) : array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/search?q=' . $name . '&type=artist', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        return $response->toArray();
    }

    public function GetArtistRecommendations(string $id) : array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/artists/' . $id . '/related-artists', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        return $response->toArray();
    }

    public function GetArtistFromId(string $id) : array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);
        return $response->toArray();
    }

    public function GetArtistAlbums(string $id) : array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/artists/' . $id . '/albums', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        return $response->toArray();
    }



}
