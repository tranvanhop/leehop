<?php
namespace Application\Form\Filter;

use Zend\InputFilter\InputFilter;

class RegistrationFilter extends InputFilter
{

    public function __construct()
    {
        $isEmpty = \Zend\Validator\NotEmpty::IS_EMPTY;

        $this->add(array(
            'name' => 'first_name',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'First Name can not be empty or too short.'
                        ),
                        'min' => 3,
                        'max' => 64
                    ),
                    'break_chain_on_failure' => true
                ),
            )
        ));

        $this->add(array(
            'name' => 'last_name',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'Last Name can not be empty or too short.'
                        ),
                        'min' => 3,
                        'max' => 64
                    ),
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'Email can not be empty.'
                        ),
                        'min' => 3,
                        'max' => 128
                    ),
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'Password can not be empty or too short.'
                        ),
                        'min' => 8,
                        'max' => 32
                    )
                )
            )
        ));

        $this->add(array(
            'name' => 'confirm_password',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'Application\Utility\Confirm',
                    'options' => array(
                        'field' => 'confirm_password'
                    )
                )
            )
        ));
    }
}