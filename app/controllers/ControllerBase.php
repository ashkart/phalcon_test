<?php

namespace App\Controller;

use App\Lib\Http\HttpException;
use App\Models\AbstractModel;
use App\Models\Visit;
use App\Models\Visitor;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    const DATE_FORMAT   = 'Y-m-d H:i:s';

    protected function _checkMethodGetOrHead()
    {
        if (!$this->request->isGet() && !$this->request->isHead()) {
            throw new \App\Lib\Http\HttpException(405, 'Not Allowed');
        }
    }

    protected function _checkMethodPost()
    {
        if (!$this->request->isPost()) {
            throw new \App\Lib\Http\HttpException(405, 'Not Allowed');
        }
    }

    protected function _viewEditAction(AbstractModel $model) : string
    {
        if ($this->request->isGet()) {
            return json_encode($model);
        } elseif ($this->request->isPost()) {
            return $this->_editModelFromPost($model);
        } else {
            throw new HttpException(405);
        }
    }

    protected function _editModelFromPost(AbstractModel $model, bool $updateOnly = true)
    {
        $post  = json_decode(file_get_contents('php://input'), JSON_OBJECT_AS_ARRAY);

        if ($updateOnly) {
            unset($post['id']);
        }

        $class = get_class($model);

        /** @var AbstractModel $model */
        $model = new $class();

        try {
            $this->db->begin();

            if ($updateOnly) {
                $modelResult = $model->save($post);
            } else {
                $modelResult = $model->create($post);
            }

            if ($modelResult) {
                $this->db->commit();
                return json_encode([], JSON_OBJECT_AS_ARRAY);
            } else {
                throw new HttpException(400);
            }
        } catch (\PDOException $e) {
            $this->db->rollback();
            throw new HttpException(400);
        }
    }
}
