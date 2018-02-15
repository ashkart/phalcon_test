<?php

use App\Controller\ControllerBase;
use App\Models\Visit;
use App\Lib\Http\HttpException;

class VisitsController extends ControllerBase
{
    public function viewEditAction()
    {
        $id    = $this->dispatcher->getParam('id');
        $visit = $this->_getVisit($id);

        return $this->_viewEditAction($visit);
    }

    public function createAction()
    {
        return $this->_editModelFromPost(new Visit(), false);
    }

    protected function _getVisit(int $id) : Visit
    {
        $visit = Visit::findFirst("id = $id");

        if (!$visit) {
            throw new HttpException('404', 'Not Found');
        }

        return $visit;
    }
}