<?php
namespace Application\Controller;

use Application\Constant\Define;

class NoteController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $this->init();

        $this->writeLog();
        return $this->_view;
    }

    public function detailAction()
    {
        $this->init();

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

    public function getItemsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if($this->_request->isGet())
        {
            $offset = (int) $this->params()->fromQuery('page', 1);
            $posts = $this->_commonDAO->executeQuery('POST_GET_ALL', array());
            $posts = $this->_utility->getPagination($posts, Define::LIMIT, $offset);
            $this->_view->setVariable('posts', $posts);
        }
        $this->_view->setTemplate('application/post/template/items.phtml');
        $this->writeLog();
        return $this->_view;
    }
    public function getItemAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if($this->getRequest()->isGet())
        {

        }
        $this->writeLog();
    }

    public function getRecentAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $userId = (int)$this->getLogin();
        $token = $this->getToken();

        if($userId > 0)
            $recent = $this->_commonDAO->executeQuery('RECENT_GET_BY_USER_ID', array($userId));
        else
            $recent = $this->_commonDAO->executeQuery('RECENT_GET_BY_TOKEN', array($token));

        $this->_view->setVariable('recent', $recent);

        $this->_view->setTemplate('application/post/template/recent.phtml');
        $this->writeLog();
        return $this->_view;
    }
}