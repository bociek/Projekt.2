<?php
/**
 * Podcast Controller.
 */

namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class PodcastController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('podcast_index');

        return $controller;
    }
}