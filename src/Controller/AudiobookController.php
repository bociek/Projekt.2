<?php
/**
 * Audiobook Controller.
 */
namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\AudiobookRepository;

class AudiobookController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('audiobook_index');

        return $controller;
    }

    public function indexAction(Application $app, $page = 1)
    {
        $audiobookRepository = new AudiobookRepository($app['db']);

        return $app['twig']->render(
            'audiobooks/index.html.twig',
            ['paginator' => $audiobookRepository->findAllPaginated($page)]
        );
    }
}