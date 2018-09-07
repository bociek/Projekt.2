<?php
/**
 * User Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\UserRepository;
/**
 * Class UserController.
 */
class UserController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('user_index');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $page = 1)
    {
        $userRepository = new UserRepository($app['db']);

       /* return $app['twig']->render(
            'albums/index.html.twig',
            ['paginator' => $albumRepository->findAllPaginated($page)]
        );*/
    }
}