<?php

namespace App\Models;

use Phalcon\Validation;

class Location extends AbstractModel
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
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $place;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $country;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $city;

    /**
     *
     * @var integer
     * @Column(type="integer", length=32, nullable=false)
     */
    public $distance;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("location");
        $this->hasMany('id', 'Visit', 'location', ['alias' => 'Visit']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'location';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Location[]|Location|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Location|\Phalcon\Mvc\Model\ResultInterface
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
            'id',
            new Validation\Validator\Digit(
                [
                    'message' => 'Invalid id',
                ]
            )
        );

        $validator->add(
            [
                'place', 'country', 'city'
            ],
            new Validation\Validator\Regex(
                [
                    'pattern' => '/^[a-zA-ZА-Яа-яёЁ\-\s]+$/u',
                    'place'   => 'Wrong place',
                    'country' => 'Wrong country',
                    'city'    => 'Wrong city',
                ]
            )
        );

        $validator->add(
            'distance',
            new Validation\Validator\Numericality(
                [
                    'message' => 'Invalid distance',
                ]
            )
        );

        return $this->validate($validator);
    }
}
