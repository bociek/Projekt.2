<?php
/**
 * Podcast Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\PodcastRepository;

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
        $controller->get('/display', [$this, 'showAction'])
            ->bind('podcast_display');

        return $controller;
    }

    public function indexAction(Application $app, $page = 1)
    {
        $podcastRepository = new PodcastRepository($app['db']);

        return $app['twig']->render(
            'podcasts/index.html.twig',
            ['paginator' => $podcastRepository->findAllPaginated($page)]
        );
    }

    /**
     * Show action.
     *
     * @param Application $app
     * @return mixed
     */
    public function showAction(Application $app)
    {
        $podcastRepository = new PodcastRepository($app['db']);

        dump($podcastRepository->showEpisodes());

        return $app['twig']->render(
            'podcasts/view.html.twig',
            ['paginator' => $podcastRepository->showEpisodes()]
        );

    }

}