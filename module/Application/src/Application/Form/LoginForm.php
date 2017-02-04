<?php
namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class LoginForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-without-legend');

        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'required' => true,
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'Enter Email'
            ),
            'options' => array(
                'label' => 'Email',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'required' => true,
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Enter Password'
            ),
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));

        // Todo : add Csrf
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Csrf',
//            'name' => 'loginCsrf',
//            'options' => array(
//                'csrf_options' => array(
//                    'timeout' => 3600
//                )
//            )
//        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-primary'
            ),
            'options' => array(
                'label' => 'Login',
            )
        ));
    }
}
