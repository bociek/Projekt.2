<?php
/**
 * MyShelf Controller.
 */

namespace Controller;

use Repository\UserRepository;
use Repository\MyshelfRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Form\AddSongType;
use Form\DeleteSongType;
use Form\AddChapterType;
use Form\AddEpisodeType;

/**
 * Class MyshelfController.
 */
class MyshelfController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])
            ->bind('myshelf_index');
        $controller->get('/addSong', [$this, 'addSongAction'])
            ->method('GET|POST')
            ->bind('song_add');
        $controller->get('/{id}/deleteSong', [$this, 'deleteSongAction'])
            ->method('GET|POST')
            ->bind('song_delete');
        $controller->get('/addChapter', [$this, 'addChapterAction'])
            ->method('GET|POST')
            ->bind('chapter_add');
        $controller->get('/{id}/deleteChapter', [$this, 'deleteChapterAction'])
            ->method('GET|POST')
            ->bind('chapter_delete');
        $controller->get('/addEpisode', [$this, 'addEpisodeAction'])
            ->method('GET|POST')
            ->bind('episode_add');
        $controller->get('/{id}/deleteEpisode', [$this, 'deleteEpisodeAction'])
            ->method('GET|POST')
            ->bind('episode_delete');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $page = 1)
    {
        $userRepository = new UserRepository($app['db']);

        $user_login = $app['security.token_storage']->getToken()->getUser()->getUsername();
        /*dump($user_login);*/

        $user_id = $userRepository->getUserIdByLogin($user_login);
        /*dump($user_id);*/
        $showAlbums = $userRepository->showAllAlbumsByUser($user_id);
        $showChapters = $userRepository->showAllChaptersByUser($user_id);
        $showEpisodes = $userRepository->showAllEpisodesByUser($user_id);
        /*dump($showAlbums);
        dump($showChapters);
        dump($showEpisodes);*/

        return $app['twig']->render(
            'myshelf/index.html.twig',
            /*['paginator' => $myshelfRepository->findAllPaginated($page)]*/
            [
                'showAlbums' => $showAlbums,
                'showChapters' => $showChapters,
                'showEpisodes' => $showEpisodes
            ]
        );
    }

    /**
     * Show My shelf.
     *
     * @param Application $app
     * @param $id
     * @return mixed
     */
    public function showMyshelf(Application $app, $id)
    {
        $myshelfRepository = new MyshelfRepository($app['db']);

        return $app['twig']->render(
            'myshelf/index.html.twig',
            ['myshelf' => $myshelfRepository->showMyshelf($id)]
        /*['paginator' => $albumRepository->showAlbum($id)]*/
        );
    }

    /**
     * Add song action.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addSongAction(Application $app, Request $request)
    {
        $userRepository = new UserRepository($app['db']);

        $user_login = $app['security.token_storage']->getToken()->getUser()->getUsername();

        $user_id = $userRepository->getUserIdByLogin($user_login);

        $songArray = [];

        $song = new MyshelfRepository($app['db']);

        $form = $app['form.factory']->createBuilder(AddSongType::class, $songArray)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $songArray = $form->getData();
            /*$album->addAlbum($albumArray);*/
            /*dump($album = $form->getData());*/

            $songArray['user_id'] = $user_id;

            if ($song->addSong($songArray)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.add_error',
                    ]
                );

            } else {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.add_complete',
                    ]
                );
                return $app->redirect($app['url_generator']->generate('myshelf_index'), 301);
            }
        }
        return $app['twig']->render(
            'myshelf/add_song.html.twig',
            [
                'song_add' => $songArray,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete song action
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteSongAction(Application $app, Request $request, $id)
    {
        $myshelfRepository = new MyshelfRepository($app['db']);

        $userRepository = new UserRepository($app['db']);

        $user_login = $app['security.token_storage']->getToken()->getUser()->getUsername();

        $user_id = $userRepository->getUserIdByLogin($user_login);

            $form = $app['form.factory']->createBuilder(DeleteSongType::class)->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $songArray = $form->getData();

                if($user_id !== $id) {
                    $app['session']->getFlashBag()->add(
                        'messages',
                        [
                            'type' => 'error',
                            'message' => 'message.cannot.delete'
                        ]
                    );

                   return $app->redirect($app['url_generator']->generate('myshelf_index'), 301);
                }

                if ($myshelfRepository->deleteSong($id)) {
                    $app['session']->getFlashBag()->add(
                        'messages',
                        [
                            'type' => 'error',
                            'message' => 'message.add_error',
                        ]

                    );

                } else {
                    $app['session']->getFlashBag()->add(
                        'messages',
                        [
                            'type' => 'success',
                            'message' => 'message.add_complete',
                        ]
                    );

                    return $app->redirect($app['url_generator']->generate('myshelf_index'), 301);
                }
            }


        return $app['twig']->render(
            'myshelf/delete_song.html.twig',
            [
                'form' => $form->createView(),
                'id' => $id,
            ]
        );
    }

    /**
     * Add chapter action.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addChapterAction(Application $app, Request $request)
    {
        $userRepository = new UserRepository($app['db']);

        $user_login = $app['security.token_storage']->getToken()->getUser()->getUsername();

        $user_id = $userRepository->getUserIdByLogin($user_login);

        $chapterArray = [];

        $chapter = new MyshelfRepository($app['db']);

        $form = $app['form.factory']->createBuilder(AddChapterType::class, $chapterArray)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chapterArray = $form->getData();
            /*$album->addAlbum($albumArray);*/
            /*dump($album = $form->getData());*/

            $chapterArray['user_id'] = $user_id;

            if ($chapter->addChapter($chapterArray)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.add_error',
                    ]
                );

            } else {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.add_complete',
                    ]
                );
                return $app->redirect($app['url_generator']->generate('myshelf_index'), 301);
            }
        }
        return $app['twig']->render(
            'myshelf/add_chapter.html.twig',
            [
                'chapter_add' => $chapterArray,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete chapter action.
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteChapterAction(Application $app, Request $request, $id)
    {
        $myshelfRepository = new MyshelfRepository($app['db']);

        $form = $app['form.factory']->createBuilder(DeleteSongType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chapterArray = $form->getData();

            if ($myshelfRepository->deleteChapter($id)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.add_error',
                    ]

                );

            } else {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.add_complete',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('myshelf_index'), 301);
            }
        }

        return $app['twig']->render(
            'myshelf/delete_chapter.html.twig',
            [
                'form' => $form->createView(),
                'id' => $id,
            ]
        );
    }

    /**
     * Add episode action.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addEpisodeAction(Application $app, Request $request)
    {
        $userRepository = new UserRepository($app['db']);

        $user_login = $app['security.token_storage']->getToken()->getUser()->getUsername();

        $user_id = $userRepository->getUserLoginById($user_login);

        $episodeArray = [];

        $episode = new MyshelfRepository($app['db']);

        $form = $app['form.factory']->createBuilder(AddEpisodeType::class, $episodeArray)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeArray = $form->getData();
            /*$album->addAlbum($albumArray);*/
            /*dump($album = $form->getData());*/

            $episodeArray['user_id'] = $user_id;

            if ($episode->addEpisode($episodeArray)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.add_error',
                    ]
                );

            } else {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.add_complete',
                    ]
                );
                return $app->redirect($app['url_generator']->generate('myshelf_index'), 301);
            }
        }
        return $app['twig']->render(
            'myshelf/add_episode.html.twig',
            [
                'episode_add' => $episodeArray,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete episode action.
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteEpisodeAction(Application $app, Request $request, $id)
    {
        $myshelfRepository = new MyshelfRepository($app['db']);

        $form = $app['form.factory']->createBuilder(DeleteSongType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episodeArray = $form->getData();

            if ($myshelfRepository->deleteEpisode($id)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.add_error',
                    ]

                );

            } else {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.add_complete',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('myshelf_index'), 301);
            }
        }

        return $app['twig']->render(
            'myshelf/delete_episode.html.twig',
            [
                'form' => $form->createView(),
                'id' => $id,
            ]
        );
    }
}