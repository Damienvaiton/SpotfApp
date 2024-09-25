<?php

namespace App\Controller;

use App\Factory\SearchedTrackFactory;
use App\service\AuthSpotifyService;
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
        private AuthSpotifyService $authSpotifyService,
        private HttpClientInterface $httpClient,

    )
    {
       $this->token =  $this->authSpotifyService->auth();
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

    #[Route('/song/{id}', name: 'app_songdetail')]
    public function detail(string $id): Response
    {
        $track = $this->GetDetailTrack($id);
        $recommendations = $this->GetRecommendationTrack($id);
        dump($recommendations);
        dump($track);
        return $this->render('song/detail.html.twig', [
            'controller_name' => 'SongController',
            'track' => $track,
            'recommendations' => $recommendations,
        ]);
    }

    #[Route('/', name: 'app_songsearch')]
    public function search(Request $request): Response
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

            $searchfactory = new SearchedTrackFactory();
            $tracks = $searchfactory->createFromSpotifyApiArray($tracksresult);

        }
        dump("tracks", $tracks);
        return $this->render('song/search.html.twig', [
                'controller_name' => 'SongController',
                'form' => $form,
                'tracks' => $tracks,
            ]);

        }





    public function GetTrack () : array
    {

        $client = HttpClient::create();
        //
        $TrackId = '5YdnOm5990Kfq1Jodws98B';

        $response = $client->request('GET', 'https://api.spotify.com/v1/tracks/'.$TrackId, [
            'headers' => [
                'authorization' => 'Bearer '.$this->token,

        ]
        ]);



        return $response->toArray();

    }

    public function GetTrackFromName(string $search) : array{
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.spotify.com/v1/search?q='.$search.'&type=track', [
            'headers' => [
                'authorization' => 'Bearer '.$this->token,
                'limit' => 10,
                ]
        ]);


        return $response->toArray();
        }

    public function GetTrackFromPlaylist () : array
    {

        $client = HttpClient::create();
        //
        $PlaylistId = '0DBSQZkaJn6DDY4eyM3044';

        $response = $client->request('GET', 'https://api.spotify.com/v1/playlists/'.$PlaylistId.'/tracks', [
            'headers' => [
                'authorization' => 'Bearer '.$this->token,

        ]
        ]);

        dump($response->toArray());

        return $response->toArray();

    }


    public function GetDetailTrack (string $id) : array
    {

        $client = HttpClient::create();
        //
        $TrackId = $id;

        $response = $client->request('GET', 'https://api.spotify.com/v1/tracks/'.$TrackId, [
            'headers' => [
                'authorization' => 'Bearer '.$this->token,

        ]
        ]);

        return $response->toArray();

    }

    public function GetRecommendationTrack (string $id) : array
    {

        $client = HttpClient::create();
        //
        $TrackId = $id;

        $response = $client->request('GET', 'https://api.spotify.com/v1/recommendations?seed_tracks='.$TrackId, [
            'headers' => [
                'authorization' => 'Bearer '.$this->token,

        ]
        ]);

        return $response->toArray();

    }

}
