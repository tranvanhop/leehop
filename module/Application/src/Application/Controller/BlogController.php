<?php
namespace Application\Controller;

use Application\Constant\Defaults;
use Application\Model\BlogTable;

class BlogController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $this->setValueDefine('Blog', 'Index');

        $categories = $this->_commonDAO->executeQuery('CATEGORIES_GET_ALL', array());
        for($i = 0; $i < count($categories); $i++)
        {
            $blog = $this->_commonDAO->executeQuery('BLOG_GET_BY_CATEGORY_ID', array($categories[$i]['id']));
            $categories[$i]['count'] = count($blog);
        }
        $this->_view->setVariable('categories', $categories);

        $this->layout()->setVariables($this->_variableLayout);
        return $this->_view;
    }

    public function getBlogAction()
    {
        $this->_view->setTerminal(true);
        $request = $this->getRequest();
        if($request->isGet())
        {
            $offset = (int) $this->params()->fromQuery('page', 1);
            $categoryId = (int) $this->params()->fromQuery('category_id', 0);
            $commonDAO = $this->getServiceLocator()->get('CommonDAO');
            $blogs = $commonDAO->executeQuery('BLOG_GET_ALL', array($categoryId));
            $blogs = $this->_utility->getPagination($blogs, Defaults::LIMIT, $offset);
            $this->_view->setVariable('blogs', $blogs);
        }
        $this->_view->setTemplate('application/blog/template/items.phtml');
        return $this->_view;
    }

    public function getItemAction()
    {
        $this->_view->setTerminal(true);

        if($this->getRequest()->isGet())
        {

        }
    }

    public function detailAction()
    {
        $this->setValueDefine('Blog', 'Index');

        $categories = $this->_commonDAO->executeQuery('CATEGORIES_GET_ALL', array());
        for($i = 0; $i < count($categories); $i++)
        {
            $blog = $this->_commonDAO->executeQuery('BLOG_GET_BY_CATEGORY_ID', array($categories[$i]['id']));
            $categories[$i]['count'] = count($blog);
        }

        $this->_view->setVariable('categories', $categories);

        $this->layout()->setVariables($this->_variableLayout);
        return $this->_view;
    }

    public function getBlogTable()
    {
        if (!$this->blogTable)
        {
            $this->blogTable = $this->getServiceLocator()->get('BlogTable');
        }
        return $this->blogTable;
    }
}