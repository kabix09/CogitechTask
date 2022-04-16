<?php

namespace App\Event\Listener;

use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Error404Listener
{

    public function onKernelException(ExceptionEvent $event)
    {
        // make sure the request is for a /posts subpage
        if (!($event->getThrowable() instanceof NotFoundHttpException || $event->getThrowable() instanceof DriverException) && !preg_match("/posts/", $event->getRequest()->getPathInfo())) {
            return;
        }

        $event->setResponse(
            new JsonResponse(
                [
                    "code" => 404,
                    "message" => "Post with this id doesn't exist"
                ]
            )
        );
    }
}