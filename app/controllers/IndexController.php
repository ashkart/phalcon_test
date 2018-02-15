<?php

use App\Controller\ControllerBase;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        return json_encode(['controller' => 'index', 'action' => 'index'], JSON_OBJECT_AS_ARRAY);
    }

    public function route404Action()
    {
        return $this->response->setStatusCode(404);
    }
}