<?php

use App\Models\Location;
use App\Models\Visit;
use App\Models\Visitor;

class CliTask extends \Phalcon\Cli\Task
{
    const KEY_USERS     = 'users';
    const KEY_LOCATIONS = 'locations';
    const KEY_VISITS    = 'visits';

    public function fillDbAction(array $args)
    {
        $beginFill = time();

        //zip file expected
        $zipFileName = $args[0];

        $zip = new \ZipArchive();

        $zip->open($zipFileName);

        $saveUser = function (array $userData) {
            $user = new Visitor($userData, $this->di, $this->modelsManager);
            $user->save();
        };

        $saveLocation = function (array $locationData) {
            $location = new Location();
            try {
                $location->save($locationData);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        };

        $saveVisit = function (array $visitData) {
            $visitData['visitor']    = $visitData['user'];

            unset($visitData['user']);

            $visit = new Visit($visitData, $this->di, $this->modelsManager);
            $visit->save();
        };

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileName    = $zip->getNameIndex($i);
            $contents    = $zip->getFromIndex($i);
            $callback    = null;
            $jsonMainKey = null;

            switch (true) {
                case (strpos($fileName, self::KEY_USERS) === 0):
                    $callback    = $saveUser;
                    $jsonMainKey = self::KEY_USERS;
                    break;

                case ((strpos($fileName, self::KEY_LOCATIONS) === 0)):
                    $callback    = $saveLocation;
                    $jsonMainKey = self::KEY_LOCATIONS;
                    break;

                case (strpos($fileName, self::KEY_VISITS) === 0):
                    $callback    = $saveVisit;
                    $jsonMainKey = self::KEY_VISITS;
            }

            if ($callback && $jsonMainKey) {
                $this->_fillDb(json_decode($contents, JSON_OBJECT_AS_ARRAY)[$jsonMainKey], $callback);
            }
        }

        $timeSpent = (time() - $beginFill);

        echo "Затрачено времени: $timeSpent секунд\n";
    }

    /**
     * @callback $getModelCb
     * @throws Exception
     * @internal param string[] $decodedJson
     */
    protected function _fillDb(array $decodedJson, $saveModelCb)
    {
        try {
            $this->db->begin();

            foreach ($decodedJson as $modelData) {
                /** @var \Phalcon\Mvc\Model $model */
                $saveModelCb($modelData);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();

            if (isset($modelData['id'])) {
                echo $modelData['id'];
            }

            throw $e;
        }
    }

    function _deleteDirectory($dir) {
        if (!file_exists($dir)) { return true; }
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') { continue; }
            if (!$this->_deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->_deleteDirectory($dir . "/" . $item)) return false;
            };
        }
        return rmdir($dir);
    }
}