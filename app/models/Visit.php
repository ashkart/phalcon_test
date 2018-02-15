<?php

namespace App\Models;

use App\Controller\ControllerBase;
use Phalcon\Validation;

class Visit extends AbstractModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=32, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=32, nullable=false)
     */
    public $location;

    /**
     *
     * @var integer
     * @Column(type="integer", length=32, nullable=false)
     */
    public $visitor;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $visited_at;

    /**
     *
     * @var integer
     * @Column(type="integer", length=16, nullable=false)
     */
    public $mark;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("visit");
        $this->belongsTo('location', '\Location', 'id', ['alias' => 'Location']);
        $this->belongsTo('visitor', '\Visitor', 'id', ['alias' => 'Visitor']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'visit';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Visit[]|Visit|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Visit|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'visited_at',
            new Validation\Validator\Numericality(
                [
                    'message' => 'Invalid timestamp',
                ]
            )
        );

        $validator->add(
            [
                'id', 'location', 'visitor'
            ],
            new Validation\Validator\Digit(
                [
                    'id' => 'Invalid id',
                    'location' => 'Invalid location id',
                    'visitor' => 'Invalid user id'
                ]
            )
        );

        $validator->add(
            'mark',
            new Validation\Validator\Between(
                [
                    "minimum" => 0,
                    "maximum" => 5,
                    "message" => "Mark out of range",
                ]
            )
        );

        return $this->validate($validator);
    }

    public function save($data = null, $whiteList = null)
    {
        if ($data) {
            $data['visitor']    = $data['user'];
            unset($data['user']);
        }

        parent::save($data, $whiteList);
    }

    public function create($data = null, $whiteList = null)
    {
        if ($data) {
            $data['visitor']    = $data['user'];
            $data['visited_at'] = date(ControllerBase::DATE_FORMAT, $data['visited_at']);
            unset($data['user']);
        }

        return parent::create($data, $whiteList);
    }
}
