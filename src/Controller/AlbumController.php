<?php
/**
 * Album Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\AlbumRepository;

/**
 * Class AlbumController.
 */
class AlbumController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('album_index');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $page = 1)
    {
        $albumRepository = new AlbumRepository($app['db']);

        return $app['twig']->render(
            'albums/index.html.twig',
            ['paginator' => $albumRepository->findAllPaginated($page)]
        );
    }

    /**
     * Search action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */

    public function searchAction(Application $app, Request $request, $album, $page = 1)
    {
        $search['album'] = $album;

        $form = $app['form.factory']->createBuilder(SearchType::class, $search)
            ->setMethod('GET')
            ->getForm();
        $form->handleRequest($request);

        $AlbumRepository = new AlbumRepository($app['db']);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();
        }
    }


}