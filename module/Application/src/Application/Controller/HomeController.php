<?php
namespace Application\Controller;

use Application\Constant\Define;
use Application\Form\CaoAnhPhuongForm;
use Application\Form\Filter\CaoAnhPhuongFilter;

class HomeController extends BaseController
{
    protected $_commonDAO;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $this->init();
        $this->_view->setVariable('define', $this->_variableLayout['define']);

        $categories = $this->_commonDAO->executeQuery('CATEGORIES_GET_ALL', array());
        for($i = 0; $i < count($categories); $i++)
        {
            $post = $this->_commonDAO->executeQuery('POST_GET_BY_CATEGORY_ID', array($categories[$i]['id']));
            $categories[$i]['count'] = count($post);
        }
        $this->_view->setVariable('categories', $categories);

        $this->writeLog();
        return $this->_view;
    }

    public function caoAnhPhuongAction()
    {
        $this->init();
        $this->_view->setVariable('define', $this->_variableLayout['define']);

        $caoAnhPhuongs = $this->_commonDAO->executeQuery('CAO_ANH_PHUONG_GET_ALL', array());
        $this->_view->setVariable('caoAnhPhuongs', $caoAnhPhuongs);

        $this->writeLog();
        return $this->_view;
    }

    public function uploadCaoAnhPhuongAction()
    {
        $this->init();

        $caoAnhPhuongForm = new CaoAnhPhuongForm('caoAnhPhuongForm');
        $caoAnhPhuongForm->setInputFilter(new CaoAnhPhuongFilter());

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            $caoAnhPhuongForm->setData($data);

            if ($caoAnhPhuongForm->isValid()) {
                $image = Define::URL_IMAGE_DEFAULT;
                if (!empty($_FILES['image'])) {
                    $filename = time() . '_' . basename($_FILES['image']['name']);
                    $image = Define::PATH_UPLOAD_IMAGES . $filename;
                    move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/' . $image);
                }

                $arrParams = array(
                    0,
                    $data['name'],
                    $image,
                    $_SERVER['REMOTE_ADDR'],
                    $_SERVER['HTTP_USER_AGENT']
                );
                $caoAnhPhuong = $this->_commonDAO->executeQuery_returnID('CAO_ANH_PHUONG_INSERT', $arrParams);
                if ($caoAnhPhuong) {
                    $this->flashMessenger()->addMessage(array(
                        'success' => Define::MESSAGE_UPLOAD_CAO_ANH_PHUONG_SUCCESS
                    ));
                    $this->writeLog();
                    return $this->redirect()->toUrl(Define::URL_REDIRECT_UPLOAD_CAO_ANH_PHUONG_SUCCESS);
                }
            }
        }
        $this->_view->setVariable('caoAnhPhuongForm', $caoAnhPhuongForm);
        $this->writeLog();
        return $this->_view;
    }

    public function getMyFriendAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if ($this->_request->isGet())
        {
            $friends = $this->_commonDAO->executeQuery('MY_FRIEND_GET_ALL', array());
            $this->_view->setVariable('friends', $friends);
        }

        $this->_view->setTemplate('application/home/template/my-friend.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getSkillsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if ($this->_request->isGet())
        {
            $skills = $this->_commonDAO->executeQuery('SKILLS_GET_ALL', array());
            $this->_view->setVariable('skills', $skills);
        }

        $this->_view->setTemplate('application/home/template/skills.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getPostsLatestAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if($this->_request->isGet())
        {
            $dateTimeQuery = $this->_utility->getDateTimeQuery('', '');
            $search = array(
                (int) $this->params()->fromQuery('id', 0),
                (int) $this->params()->fromQuery('category_id', 0),
                $this->params()->fromQuery('title', ''),
                $this->params()->fromQuery('summary', ''),
                $this->params()->fromQuery('description', ''),
                $this->params()->fromQuery('thumbnail', ''),
                $this->params()->fromQuery('start_date', $dateTimeQuery['start_date']),
                $this->params()->fromQuery('end_date', $dateTimeQuery['end_date']),
                (int) $this->params()->fromQuery('create_by', 1),
                $this->params()->fromQuery('tags', ''),
                (int) $this->params()->fromQuery('limit', Define::LIMIT_HOME),
                (int) $this->params()->fromQuery('offset', 0)
            );

            $posts = $this->_commonDAO->executeQuery('POST_SEARCH', $search);
            $this->_view->setVariable('posts', $posts);
        }
        $this->_view->setTemplate('application/home/template/posts-latest.phtml');
        $this->writeLog();
        return $this->_view;
    }
}