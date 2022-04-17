<?php

namespace App\Event\Listener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        $response->headers->add([
            'Strict-Transport-Security' => "max-age=63072000; includeSubdomains; preload",  // ensure HSTS directive
            'Content-Security-Policy' => "default-src 'self'; img-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net www.w3.org;", // header helping reduce XSS risks, it allows to restrict which resources load
            'x-content-type-options' => "nosniff",  // mean that Content-Type header should be followed and not be changed
            'x-frame-options' => "SAMEORIGIN",  // X-Frame-Options header prevents your site content embedded into other sites
            'x-xss-protection' => "1; mode=block"
        ]);

    }
}