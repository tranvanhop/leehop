<?php
namespace Application\Controller;

use Application\Constant\Define;
use Application\Form\ContactForm;
use Application\Form\Filter\ContactFilter;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class ContactController extends BaseController
{
    protected $_commonDAO;

    public function __construct()
    {
        parent::__construct();
    }
    public function indexAction()
    {
        $this->init();
        $this->_view->setVariable('define', $this->_define);

        $contactForm = new ContactForm('contactForm');
        $contactForm->setInputFilter(new ContactFilter());

        if($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            $contactForm->setData($data);

            if($contactForm->isValid())
            {
                $contact = array(
                    $data['name'],
                    $data['email'],
                    $data['message'],
                    $_SERVER['REMOTE_ADDR'],
                    date('Y-m-d H:i:s')
                );
                $result = $this->_commonDAO->executeQuery_returnID('CONTACT_INSERT', $contact);
                if($result)
                {
                    $this->flashMessenger()->addMessage(array(
                        'success' => Define::MESSAGE_CONTACT_SEND_SUCCESS
                    ));
                    // Send email
                    $transport = $this->getServiceLocator()->get('mail');
                    $message = new Message();

                    $render = $this->getServiceLocator()->get('ViewRenderer');
                    $params = array(
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'message' => $data['message']
                    );
                    $content = $render->render('application/contact/template/email', $params);

                    $html = new MimePart($content);
                    $html->type = "text/html";
                    $body = new MimeMessage();
                    $body->setParts(array($html));

                    $message->addTo($this->_define['email'])
                        ->addFrom($this->_define['email'])
                        ->setSubject('LeeHop - Contact')
                        ->setBody($body);
                    $transport->send($message);

                    // End send email
                    return $this->redirect()->toUrl(Define::URL_REDIRECT_CONTACT_SUCCESS);
                }
                $this->flashMessenger()->addMessage(array(
                    'danger' => Define::MESSAGE_CONTACT_SEND_FAIL
                ));
                return $this->redirect()->toUrl(Define::URL_REDIRECT_CONTACT_FAIL);
            }
        }

        $this->_view->setVariable('contactForm', $contactForm);

        $this->writeLog();
        return $this->_view;
    }
}