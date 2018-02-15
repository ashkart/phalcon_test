<?php

use App\Controller\ControllerBase;
use App\Models\Location;
use App\Lib\Http\HttpException;

class LocationsController extends ControllerBase
{
    public function viewEditAction()
    {
        $id       = $this->dispatcher->getParam('id');
        $location = $this->_getLocation($id);

        return $this->_viewEditAction($location);
    }

    public function createAction()
    {
        return $this->_editModelFromPost(new Location(), false);
    }

    public function avgMarkAction()
    {
        $this->_checkMethodGetOrHead();

        $id       = $this->dispatcher->getParam('id');
        $this->_getLocation($id);

        $queryParams = $this->request->getQuery();

        $fromDate    = $queryParams['fromDate'];
        $toDate      = $queryParams['toDate'];
        $fromAge     = $queryParams['fromAge'];
        $toAge       = $queryParams['toAge'];
        $gender      = $queryParams['gender'];

        $filterStatement = '';

        if ($fromDate) {
            $fromDate         = date(ControllerBase::DATE_FORMAT, $fromDate);

            if (!$fromDate) {
                throw new HttpException(400, 'Bad Request');
            }

            $filterStatement .= " AND v.visited_at > '$fromDate'";
        }

        if ($toDate) {
            $toDate           = date(ControllerBase::DATE_FORMAT, $toDate);

            if (!$toDate) {
                throw new HttpException(400, 'Bad Request');
            }

            $filterStatement .= " AND v.visited_at < '$toDate'";
        }

        if ($fromAge) {
            if (!is_numeric($fromAge)) {
                throw new HttpException(400, 'Bad Request');
            }

            $filterStatement .= " AND (now() - u.birth_date) > INTERVAL '$fromAge years'";
        }

        if ($toAge) {
            if (!is_numeric($toAge)) {
                throw new HttpException(400, 'Bad Request');
            }

            $filterStatement .= " AND (now() - u.birth_date) < INTERVAL '$toAge years'";
        }

        if ($gender) {
            if (in_array($gender, ['m', 'f'])) {
                throw new HttpException(400);
            }

            $filterStatement .= "AND u.gender = '$gender'";
        }

        $sql = "
            SELECT AVG(v.mark) \"avg\"
            FROM location l
            LEFT JOIN visit v on v.location = l.id
            JOIN visitor u ON v.visitor = u.id
            WHERE l.id = $id
            $filterStatement            
        ";

        $query = $this->db->query($sql);
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);

        $result = $query->fetchAll();

        if (!$result[0]['avg']) {
            $result[0]['avg'] = 0;
        } else {
            $result[0]['avg'] = round($result[0]['avg'], 5, PHP_ROUND_HALF_UP);
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    protected function _getLocation(int $id) : Location
    {
        $location = Location::findFirst("id = $id");

        if (!$location) {
            throw new HttpException('404', 'Not Found');
        }

        return $location;
    }
}