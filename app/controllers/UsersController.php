<?php

use App\Controller\ControllerBase;
use App\Models\Visitor;
use App\Lib\Http\HttpException;

class UsersController extends ControllerBase
{
    public function viewEditAction()
    {
        $id   = $this->dispatcher->getParam('id');
        $user = $this->_getUser($id);

        return $this->_viewEditAction($user);
    }

    public function createAction()
    {
        return $this->_editModelFromPost(new Visitor(), false);
    }

    public function visitsAction()
    {
        $this->_checkMethodGetOrHead();

        $id   = $this->dispatcher->getParam('id');
        $this->_getUser($id);

        $queryParams = $this->request->getQuery();

        $fromDate    = $queryParams['fromDate'];
        $toDate      = $queryParams['toDate'];
        $country     = $queryParams['country'];
        $toDistance  = $queryParams['toDistance'];

        $filterStatement = '';

        if ($fromDate) {
            if (!$fromDate) {
                throw new HttpException(400, 'Bad Request');
            }

            $filterStatement .= " AND v.visited_at > '$fromDate'";
        }

        if ($toDate) {
            if (!$toDate) {
                throw new HttpException(400, 'Bad Request');
            }

            $filterStatement .= " AND v.visited_at < '$toDate'";
        }

        if ($country) {
            $filterStatement .= " AND l.country = '$country'";
        }

        if ($toDistance) {

            if (!is_numeric($toDistance)) {
                throw new HttpException(400, 'Bad Request');
            }

            $filterStatement .= " AND l.distance < $toDistance";
        }

        $sql = "
          select v.mark, v.visited_at, l.place from visit v
          join location l on l.id = v.location
          where v.visitor = $id
          $filterStatement
        ";

        $query = $this->db->query($sql);
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);

        $result = $query->fetchAll();

        return json_encode(['visits' => $result]);
    }

    protected function _getUser(int $id) : Visitor
    {
        $user = Visitor::findFirst("id = $id");

        if (!$user) {
            throw new HttpException('404', 'Not Found');
        }

        return $user;
    }
}