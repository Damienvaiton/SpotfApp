<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Factory\ArtistFactory;
use App\service\AuthSpotifyService;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/searchedArtist', name: 'app_searchedArtist')]
    public function searchedArtist(Request $request, Security $security): Response
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


            foreach ($artists as $artist) {
                $userfavorite = $security->getUser()->getFavoriteArtists();
                foreach ($userfavorite as $fav) {
                    if ($artist->getIdspotify() === $fav->getIdspotify()) {
                        $artist->setIsFavorite(true);
                    }
                }
            }

        }
        return $this->render('artist/searchedArtist.html.twig', [
            'form' => $form->createView(),
            'artists' => $artists,
        ]);
    }

    public function GetArtistFromName(string $name): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/search?q=' . $name . '&type=artist', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        return $response->toArray();
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

    public function GetArtistFromId(string $id): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);
        return $response->toArray();
    }

    public function GetArtistAlbums(string $id): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/artists/' . $id . '/albums', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        return $response->toArray();
    }

    public function GetArtistRecommendations(string $id): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/artists/' . $id . '/related-artists', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        return $response->toArray();
    }

    #[Route('/Afavorite', name: 'app_personnal_favoriteArtist')]
    public function FavoriteArtistList(Security $security): Response
    {
        $user = $security->getUser();
        $artists = $user->getFavoriteArtists();
        dump($artists);
        return $this->render('artist/favorite.html.twig', [
            'controller_name' => 'ArtistController',
            'artists' => $artists,
        ]);
    }

    #[Route('/favoriteArtist/{id}', name: 'app_favoriteArtist')]
    public function favoriteArtist(EntityManagerInterface $entityManager, string $id, Security $security): Response
    {
        $user = $security->getUser();
        $artist = $this->GetArtistFromId($id);

        $artistFactory = new ArtistFactory();
        $artist = $artistFactory->createfromAPI($artist);

        $findArtist = $entityManager->getRepository(Artist::class)->findOneBy(['idspotify' => $artist->getIdspotify()]);
        $userFavoriteArtist = $user->getFavoriteArtists();

        if ($findArtist === null) {
            $entityManager->persist($artist);
            $user->addFavoriteArtist($artist);
            $entityManager->flush();
        } else {
            foreach ($userFavoriteArtist as $item) {
                if ($item->getIdspotify() === $artist->getIdspotify()) {
                    $user->removeFavoriteArtist($item);
                    $entityManager->flush();
                    return $this->redirectToRoute('app_artist_details', ['id' => $id]);
                }

            }
        }
        return $this->redirectToRoute('app_artist_details', ['id' => $id]);
    }


}
