<?php
/**
 * Album Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\AdminRepository;
/**
 * Class AdminController.
 */
class AdminController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('admin_index');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $page = 1)
    {
        $adminRepository = new AdminRepository($app['db']);

       /* return $app['twig']->render(
            'albums/index.html.twig',
            ['paginator' => $albumRepository->findAllPaginated($page)]
        );*/
    }
}