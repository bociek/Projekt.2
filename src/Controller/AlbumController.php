<?php
/**
 * Album Controller.
 */

namespace Controller;

use Repository\UserRepository;
use Form\AddAlbumType;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\AlbumRepository;
use Symfony\Component\HttpFoundation\Request;
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
        $controller->get('/add', [$this, 'addAlbum'])
            ->method('GET|POST')
            ->bind('album_add');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $page = 1)
    {
        $albumRepository = new AlbumRepository($app['db']);

        /*dump($albumRepository->showAlbum());*/

        return $app['twig']->render(
            'albums/index.html.twig',
            /*['paginator' => $albumRepository->findAllPaginated($page)]*/
            ['album' => $albumRepository->showAlbum()]
        );
    }

    /**
     * Add album.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAlbum(Application $app, Request $request)
    {
        $albumArray = [];
        $album = new AlbumRepository($app['db']);

        $form = $app['form.factory']->createBuilder(AddAlbumType::class, $albumArray)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumArray = $form->getData();
            /*$album->addAlbum($albumArray);*/
            /*dump($album = $form->getData());*/

        if ($album->addAlbum($albumArray)) {
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

            return $app->redirect($app['url_generator']->generate('homepage'), 301);
        }

    }

        return $app['twig']->render(
            'albums/add.html.twig',
            [
                'album' => $albumArray,
                'form' => $form->createView(),
            ]
        );
    }

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
     * Shows interactive table.
     *
     * @param Application $app
     * @param $id
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