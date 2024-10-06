<?php

namespace App\Controller;

use App\Entity\Track;
use App\Factory\TrackFactory;
use App\service\AuthSpotifyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class SongController extends AbstractController
{

    private string $token;

    public function __construct(
        private readonly AuthSpotifyService $authSpotifyService,

    )
    {
        $this->token = $this->authSpotifyService->auth();
    }


    #[Route('/song', name: 'app_song')]
    public function index(): Response
    {

        return $this->render('song/index.html.twig', [
            'controller_name' => 'SongController',
            'playlist' => $this->GetTrackFromPlaylist(),
            'track' => $this->GetTrack(),
        ]);
    }

    public function GetTrackFromPlaylist(): array
    {

        $client = HttpClient::create();
        //
        $PlaylistId = '0DBSQZkaJn6DDY4eyM3044';

        $response = $client->request('GET', 'https://api.spotify.com/v1/playlists/' . $PlaylistId . '/tracks', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,

            ]
        ]);

        dump($response->toArray());

        return $response->toArray();

    }

    public function GetTrack(): array
    {

        $client = HttpClient::create();
        //
        $TrackId = '5YdnOm5990Kfq1Jodws98B';

        $response = $client->request('GET', 'https://api.spotify.com/v1/tracks/' . $TrackId, [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,

            ]
        ]);


        return $response->toArray();

    }

    #[Route('/song/{id}', name: 'app_songdetail')]
    public function detail(string $id): Response
    {

        $trackresult = $this->GetDetailTrack($id);

        $recommendations = $this->GetRecommendationTrack($id);

        $trackfactory = new TrackFactory();
        $track = $trackfactory->createfromAPI($trackresult);
        dump($track);

        return $this->render('song/detail.html.twig', [
            'controller_name' => 'SongController',
            'track' => $track,
            'recommendations' => $recommendations,
        ]);
    }



// Fonctions GET
//
//
//

    public function GetDetailTrack(string $id): array
    {

        $client = HttpClient::create();
        //
        $TrackId = $id;

        $response = $client->request('GET', 'https://api.spotify.com/v1/tracks/' . $TrackId, [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,

            ]
        ]);

        return $response->toArray();

    }

    public function GetRecommendationTrack(string $id): array
    {

        $client = HttpClient::create();
        $TrackId = $id;

        $response = $client->request('GET', 'https://api.spotify.com/v1/recommendations?seed_tracks=' . $TrackId, [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,

            ]
        ]);

        return $response->toArray();

    }

    #[Route('/favorite/{id}', name: 'app_favorite')]
    public function Favorite(EntityManagerInterface $entityManager, string $id)
    {
        $track = $this->GetTrackFromId($id);

        $firdtrack = $entityManager->getRepository(Track::class)->findOneBy(['id' => $id]);
        if ($firdtrack === null) {
            $track->setFavorite(true);
            $entityManager->persist($track);
        } else {
            $firdtrack->setFavorite(false);
            $entityManager->remove($firdtrack);
        }
        $entityManager->flush();


        return $this->redirectToRoute('app_songdetail', ['id' => $id]);


    }

    public function GetTrackFromId(string $id): Track
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/tracks/' . $id, [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        $track = $response->toArray();
        $trackfactory = new TrackFactory();
        $result = $trackfactory->createfromAPI($track);
        dump($result);
        return $result;

    }

    #[Route('/', name: 'app_songsearch')]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tracks = [];
        $form = $this->createFormBuilder()
            ->add('search', TextType::class, [
                'label' => 'Search for a song',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter a song name',
                    'required' => false,

                ]
            ])
            ->add('submit', SubmitType::class)
            ->setMethod('GET')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $search = $data['search'];

            if (empty($search)) {
                return $this->redirectToRoute('app_song');
            }

            $tracksresult = $this->GetTrackFromName($search);


            $trackfactory = new TrackFactory();
            $tracks = $trackfactory->createfromAPIArray($tracksresult['tracks']);
            foreach ($tracks as $track) {
                $firdtrack = $entityManager->getRepository(Track::class)->findOneBy(['id' => $track->getId()]);
                if ($firdtrack !== null) {
                    $track->setFavorite(true);
                }
            }
        }
        return $this->render('song/search.html.twig', [
            'controller_name' => 'SongController',
            'form' => $form,
            'tracks' => $tracks,
        ]);

    }

    public function GetTrackFromName(string $search): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/search?q=' . $search . '&type=track', [
            'headers' => [
                'authorization' => 'Bearer ' . $this->token,
            ]
        ]);


        return $response->toArray();
    }

}
