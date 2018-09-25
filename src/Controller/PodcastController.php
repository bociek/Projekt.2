<?php
/**
 * Podcast Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\PodcastRepository;
use Symfony\Component\HttpFoundation\Request;
use Form\AddPodcastType;

class PodcastController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])
            ->bind('podcast_index');
        $controller->get('/{id}/display', [$this, 'showAction'])
            ->bind('podcast_display');
        $controller->get('/add', [$this, 'addPodcast'])
            ->method('GET|POST')
            ->bind('podcast_add');
        $controller->get('/new', [$this, 'showEcho'])
            ->method('GET|POST')
            ->bind('new_echo');

        return $controller;
    }

    public function indexAction(Application $app, $page = 1)
    {
        $podcastRepository = new PodcastRepository($app['db']);

        return $app['twig']->render(
            'podcasts/index.html.twig'
           /* ['paginator' => $podcastRepository->findAllPaginated($page)]*/
        );
    }

    /**
     * Show action.
     *
     * Shows interactive table.
     *
     * @param Application $app
     * @param $id
     * @return mixed
     */
    public function showAction(Application $app, $id)
    {
        $podcastRepository = new PodcastRepository($app['db']);

        return $app['twig']->render(
            'podcasts/view.html.twig',
            ['podcast' => $podcastRepository->showEpisodes($id)]
            /*['paginator' => $podcastRepository->showEpisodes()]*/
        );

    }

    /**
     * Add podcast.
     *
     * @param \Silex\Application $app Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addPodcast(Application $app, Request $request)
    {
        $podcastArray = [];
        $podcast = new PodcastRepository($app['db']);

        $form = $app['form.factory']->createBuilder(AddPodcastType::class, $podcastArray)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $podcastArray = $form->getData();

            if ($podcast->addPodcast($podcastArray)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.add_error',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('homepage'), 301);
            } else {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.add_complete',
                    ]
                );
            }
        }

        return $app['twig']->render(
            'podcasts/add.html.twig',
            [
                'podcast' => $podcastArray,
                'form' => $form->createView(),
            ]
        );


    }

    public function showEcho(Application $app)
    {
        return dump('echo');
    }

}
