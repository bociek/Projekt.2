<?php
/**
 * Photo controller.
 */
namespace Controller;

use Form\PhotoType;
use Repository\PhotoRepository;
use Service\FileUploader;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PhotosController.
 */
class PhotoController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])
            ->bind('photo_index');

        return $controller;
    }

    /**
     * Index action.
     *
     */
    public function indexAction(Application $app, $id, $page = 1)
    {
        $photoRepository = new PhotoRepository($app['db']);

        return $app['twig']->render(
            'images/index.html.twig',
            /*['paginator' => $albumRepository->findAllPaginated($page)]*/
            ['photo' => $photoRepository->save($id)]
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
    public function addAction(Application $app, Request $request)
    {
        $photo = [];

        $form = $app['form.factory']->createBuilder(PhotoType::class, $photo)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo  = $form->getData();
            $fileUploader = new FileUploader($app['config.photos_directory']);
            $fileName = $fileUploader->upload($photo['photo']);
            $photo['photo'] = $fileName;
            $photosRepository = new PhotosRepository($app['db']);
            $photosRepository->save($photo);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('photos_index'),
                301
            );
        }

        return $app['twig']->render(
            'photo/add.html.twig',
            [
                'photo'  => $photo,
                'form' => $form->createView(),
            ]
        );
    }
}
