<?php
/**
 * Album Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\AlbumRepository;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

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
        $controller->get('/', [$this, 'indexAction'])
            ->bind('album_index');
        $controller->get('/{id}/display', [$this, 'showAction'])
            ->bind('album_display');

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
     * Add action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
   /* public function addAction(Application $app, Request $request)
    {
        $album = [];

        $form = $app['form.factory']->createBuilder(AlbumType::class, $album)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            dump($tag);
        }

        return $app['twig']->render(
            'albums/add.html.twig',
            [
                'album' => $album,
                'form' => $form->createView(),
            ]
        );
    }*/

    /**
     * Search action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */

    /*public function searchAction(Application $app, Request $request, $album, $page = 1)
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
        return $app['twig']->render(
            'albums/search.html.twig',
            [
                'search' => $search,
                'form' => $form->createView(),
                'paginator' => $AlbumRepository->searchPaginated($search,$page),
            ]
        );
    }*/

    /**
     * Show action.
     *
     * @param Application $app
     * @return mixed
     */
    public function showAction(Application $app, $id)
    {
        $albumRepository = new AlbumRepository($app['db']);

        return $app['twig']->render(
            'albums/view.html.twig',
            ['album' => $albumRepository->showAlbum($id)]
            /*['paginator' => $albumRepository->showAlbum($id)]*/
        );

    }

}