<?php

use App\Controller\ControllerBase;

class ErrorController extends ControllerBase
{
    public function show404Action()
    {
        $this->response->setStatusCode(404);
    }
}