<?php
/**
 * User Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Form\EditUserType;
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
        $controller->match('/{id}/edit', [$this, 'editUserData'])
            ->method('GET|POST')
            ->bind('user_edit');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $page = 1)
    {
        return $app['twig']->render(
            'users/index.html.twig'
            /*['showAll' => $showAll]*/
        );
    }

    /**
     * Edit user data.
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function editUserData(Application $app, Request $request, $id)
    {
        $userRepository = new UserRepository($app['db']);

        $user_data = $userRepository->showUserData($id);

        /*dump($id);*/

        $user_array['fname'] = $user_data[0]['fname'];
        $user_array['lname'] = $user_data[0]['lname'];
        $user_array['email'] = $user_data[0]['email'];
        $user_array['country'] = $user_data[0]['country'];

        $form =$app['form.factory']->createBuilder(EditUserType::class, $user_array)->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $update_data = $form->getData();
            dump($update_data);
            $userRepository->updateUserData($update_data, $id);
        }

        return $app['twig']->render(
            'users/edit.html.twig',
            [
                'showAll' => $user_array,
                'form' => $form->createView(),
                'id' => $id,
            ]
        );
    }
}