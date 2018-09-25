<?php
/**
 * Album Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\AdminRepository;
use Form\EditUserType;
use Form\DeleteType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController.
 */
class AdminController implements ControllerProviderInterface
{
    /**
     * See list of users, can edit user's data, can delete user
     *
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])
            ->bind('admin_index');
        $controller->match('/{id}/edit', [$this, 'editUserData'])
            ->method('GET|POST')
            ->bind('user_edit');
        $controller->match('/{user_id}/delete', [$this, 'deleteUser'])
            ->method('GET|POST')
            ->bind('user_delete');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $page = 1)
    {
        $adminRepository = new AdminRepository($app['db']);

        $showAll = $adminRepository->showAllUsers();

        /*dump($showAll);*/

        return $app['twig']->render(
            'users/index.html.twig',
            /*['paginator' => $adminRepository->findAllPaginated($page)]*/
            ['showAll' => $showAll]
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
        $adminRepository = new AdminRepository($app['db']);

        $user_data = $adminRepository->showUserData($id);

        /*dump($user_data[0]);*/

        $user_array['fname'] = $user_data[0]['fname'];
        $user_array['lname'] = $user_data[0]['lname'];
        $user_array['email'] = $user_data[0]['email'];
        $user_array['country'] = $user_data[0]['country'];

        $form =$app['form.factory']->createBuilder(EditUserType::class, $user_array)->getForm();
        $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $user_data = $form->getData();
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

    /**
     * Delete user.
     *
     * @param Application $app
     * @param Request $request
     * @param $user_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(Application $app, Request $request, $user_id)
    {
        $adminRepository = new AdminRepository($app['db']);

        $form = $app['form.factory']->createBuilder(DeleteType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user_data = $form->getData();

            if ($adminRepository->deleteUser($user_id)) {
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
            'users/delete.html.twig',
            [
                'form' => $form->createView(),
                'id' => $user_id,
            ]
        );
    }

   /* public function editUserData(Application $app, Request $request, $id)
    {
        $adminRepository = new AdminRepository($app['db']);

        $form = $app['form.factory']->createBuilder(RegisterType::class, $id)->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $login = $form->getData();
            $user_data['password'] = $app['security.encoder.bcrypt']->encodePassword($user_data['password'], '');
            $adminRepository->updateData($user_data, $login);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.user_data_edited'
                ]
            );

            return $app->redirect($app['url_generator']->generate('homepage'), 301);
        }

        return $app['twig']->render(
            'users/edit.html.twig',
            [
                'tag' => $login,
                'form' => $form->createView(),
            ]
        );
    }*/

    /*public function deleteUser(Application $app, Request $request, $id)
    {
        $adminRepository = new AdminRepository($app['db']);

        $form = $app['form.factory']->createBuilder(DeleteType::class)->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $adminRepository->deleteUser($id);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.user_data_deleted'
                ]
            );

            return $app->redirect($app['url_generator']->generate('homepage'), 301);
        }

        return $app['twig']->render(
            'users/delete.html.twig',
            [
                'tag' => $login,
                'form' => $form->createView(),
            ]
        );
    }*/
}