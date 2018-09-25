<?php
/**
 * Audiobook Controller.
 */
namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\AudiobookRepository;
use Symfony\Component\HttpFoundation\Request;
use Form\AddAudiobookType;

class AudiobookController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])
            ->bind('audiobook_index');
        $controller->get('/{id}/display', [$this, 'showAction'])
            ->bind('audiobook_display');
        $controller->get('/add', [$this, 'addAudiobook'])
            ->method('GET|POST')
            ->bind('audiobook_add');

        return $controller;
    }

    public function indexAction(Application $app, $page = 1)
    {
        $audiobookRepository = new AudiobookRepository($app['db']);

        return $app['twig']->render(
            'audiobooks/index.html.twig'
           /* ['paginator' => $audiobookRepository->findAllPaginated($page)]*/
        );
    }

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
        $audiobookRepository = new AudiobookRepository($app['db']);

        return $app['twig']->render(
            'audiobooks/view.html.twig',
            ['audiobook' => $audiobookRepository->showChapters($id)]
            /*['paginator' => $audiobookRepository->showChapters()]*/
        );

    }

    /**
     * Add audiobook.
     *
     * @param \Silex\Application $app Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAudiobook(Application $app, Request $request)
    {
        $audiobookArray = [];
        $audiobook = new AudiobookRepository($app['db']);

        $form = $app['form.factory']->createBuilder(AddAudiobookType::class, $audiobookArray)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $audiobookArray = $form->getData();

            if ($audiobook->addAudiobook($audiobookArray)) {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.add_error',
                    ]
                );

                return $app->redirect($app['url_generator']->generate('homepage'), 301);
            } else {
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'error',
                        'message' => 'message.add_complete',
                    ]
                );
            }

        }

        return $app['twig']->render(
            'audiobooks/add.html.twig',
            [
                'audiobook' => $audiobookArray,
                'form' => $form->createView(),
            ]
        );
    }

}
