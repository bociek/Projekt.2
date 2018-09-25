<?php
/**
 * Homepage controller.
 */
namespace Controller;

use Repository\UserRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

/**
 * Class HomepageController.
 */
class HomepageController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('homepage');

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function indexAction(Application $app)
    {
        //$homepageRepository = new HomepageRepository($app['db']);
        $userRepository = new UserRepository($app['db']);

        /*dump($userRepository->getUserIdByLogin($user_login)[0]);*/

        return $app['twig']->render(
            'main/index.html.twig',
            /*['tags' => $homepageRepository->findAll()]*/
            ['']
        );
    }
}