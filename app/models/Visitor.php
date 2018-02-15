<?php

namespace App\Models;

use App\Controller\ControllerBase;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class Visitor extends AbstractModel
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
     * @Column(type="string", length=200, nullable=false)
     */
    public $email;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $last_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $gender;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $birth_date;

    public $_validationMessages;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        $validator->add(
            'gender',
            new Validation\Validator\Regex(
                [
                    "pattern" => "/^[fm]$/",
                    "message" => "The gender value is invalid",
                ]
            )
        );

        $validator->add(
            'birth_date',
            new Validation\Validator\Numericality(
                [
                    'message' => 'Invalid timestamp',
                ]
            )
        );

        $validator->add(
            [
                'first_name',
                'last_name'
            ],
            new Validation\Validator\Alpha(
                [
                    "first_name" => "Invalid first_name",
                    "last_name"  => "Invalid last_name",
                ]
            )
        );

        $validationResult = $this->validate($validator);

        if (!$validationResult) {
            $this->_validationMessages = $validator->getMessages();
        }

        return $validationResult;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("visitor");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'visitor';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Visitor[]|Visitor|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Visitor|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
