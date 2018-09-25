<?php
/**
 * Register controller.
 */

namespace Controller;

use Form\RegisterType;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\UserRepository;

class RegisterController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'registerAction'])
            ->method('GET|POST')
            ->bind('auth_register');

        return $controller;
    }

    /**
     * Register action.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function registerAction(Application $app, Request $request)
    {
        $register = [];

        $form = $app['form.factory']->createBuilder(RegisterType::class, $register)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository = new UserRepository($app['db']);
            $register = $form->getData();
            $register['password'] = $app['security.encoder.bcrypt']->encodePassword($register['password'], '');


            if ($userRepository->registerUser($register)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.registration_complete',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('homepage'), 301);
            }
                else
                {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.registration_error',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('homepage'), 301);
            }
        }

        return $app['twig']->render(
            'register/register.html.twig',
            [
                'register' => $register,
                'form' => $form->createView(),
            ]
        );
    }
}