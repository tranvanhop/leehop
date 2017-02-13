<?php
namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class CaoAnhPhuongForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-without-legend');

        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
            'attributes' => array(
                'id' => 'id'
            )
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'Title'
            ),
            'options' => array(
                'label' => 'Title',
                'label_attributes' => array(
                    'class' => 'col-lg-4 control-label'
                ),
            )
        ));
        $this->add(array(
            'name' => 'image',
            'type' => 'file',
            'attributes' => array(
                'id' => 'image',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Image',
                'label_attributes' => array(
                    'class' => 'col-lg-4 control-label'
                ),
            )
        ));
        // Todo : add Csrf
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Csrf',
//            'name' => 'contactCsrf',
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
                'label' => 'Submit',
            )
        ));
    }
}
