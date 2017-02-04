<?php
namespace Application\Controller;

use Application\Constant\Define;
use Application\Form\CommentForm;
use Application\Form\Filter\CommentFilter;

class PostController extends BaseController
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

        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $date = $this->_utility->getDateTimeQuery('', '');
        $post = $this->_commonDAO->executeQueryFirst('POST_SEARCH', array(
            intval($id),
            0, // category_id
            '', // title
            '', // summary
            '', // description
            '', // thumbnail
            $date['start_date'], // start date
            $date['end_date'], // end date
            0, // create_by
            '', // tags
            1, // limit
            0 // offset
        ));
        $this->_view->setVariable('post', $post);

        // Get comment
        $comments = $this->_commonDAO->executeQuery('COMMENT_GET_BY_POST_ID', array($id));
        $this->_view->setVariable('comments', $comments);

        // Comment
        $commentForm = new CommentForm('commentForm');
        $commentForm->setInputFilter(new CommentFilter());
        $comment = array(
            'post_id' => $id,
            'user_id' => 0,
            'comment_id' => 0
        );

        if($this->getLogin())
        {
            $userId = $this->getLogin();
            $user = $this->_commonDAO->executeQueryFirst('USER_GET_BY_ID', array($userId));
            if($user)
            {
                $comment['name'] = $user['first_name'] . ' ' . $user['last_name'];
                $comment['email'] = $user['email'];
                $comment['user_id'] = $userId;
            }
        }
        $commentForm->setData($comment);

        if ($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            $commentForm->setData($data);

            if($commentForm->isValid())
            {
                $arrParams = array(
                    empty($data['post_id']) ? $id : $data['post_id'],
                    empty($data['user_id']) ? 0 : $data['user_id'],
                    empty($data['comment_id']) ? 0 : $data['comment_id'],
                    $data['name'],
                    $data['email'],
                    $data['message']
                );
                $result = $this->_commonDAO->executeQuery_returnID('COMMENT_INSERT', $arrParams);
                if($result)
                {
                    $this->flashMessenger()->addMessage(array(
                        'success' => Define::MESSAGE_COMMENT_SUCCESS
                    ));
                    return $this->redirect()->toUrl(Define::URL_REDIRECT_COMMENT_SUCCESS.$id);
                }
                else
                {
                    $this->flashMessenger()->addMessage(array(
                        'danger' => Define::MESSAGE_COMMENT_FAIL
                    ));
                    return $this->redirect()->toUrl(Define::URL_REDIRECT_COMMENT_FAIL);
                }
            }
        }
        $this->_view->setVariable('commentForm', $commentForm);

        // Set Recent
        $arrParamRecent = array(
            $id,
            !$this->getLogin() ? 0 : $this->getLogin(),
            $this->getToken()
        );
        $this->_commonDAO->executeQuery_returnID('RECENT_INSERT', $arrParamRecent);

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
                (int) $this->params()->fromQuery('limit', Define::LIMIT_MAX),
                (int) $this->params()->fromQuery('offset', 0)
            );
            $posts = $this->_commonDAO->executeQuery('POST_SEARCH', $search);
            $posts = $this->_utility->getPagination($posts, Define::LIMIT, $offset);
            $this->_view->setVariable('posts', $posts);
        }
        $this->_view->setTemplate('application/post/template/items.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getRecentAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $userId = (int)$this->getLogin();
        $token = $this->getToken();

        if($userId > 0)
            $recent = $this->_commonDAO->executeQuery('RECENT_GET_BY_USER_ID', array($userId, Define::LIMIT_RECENT));
        else
            $recent = $this->_commonDAO->executeQuery('RECENT_GET_BY_TOKEN', array($token, Define::LIMIT_RECENT));

        $this->_view->setVariable('recent', $recent);

        $this->_view->setTemplate('application/post/template/recent.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getTagsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $tags = $this->_commonDAO->executeQuery('TAG_GET_ALL', array());
        $this->_view->setVariable('tags', $tags);

        $this->_view->setTemplate('application/post/template/tags.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getCategoriesAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $categories = $this->_commonDAO->executeQuery('CATEGORIES_GET_ALL', array());
        for($i = 0; $i < count($categories); $i++)
        {
            $post = $this->_commonDAO->executeQuery('POST_GET_BY_CATEGORY_ID', array($categories[$i]['id']));
            $categories[$i]['count'] = count($post);
        }
        $this->_view->setVariable('category_id', (int)$this->params()->fromQuery('category_id', 0));
        $this->_view->setVariable('categories', $categories);

        $this->_view->setTemplate('application/post/template/categories.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getCommentsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if ($this->getRequest()->isGet()) {
            $postId = $this->getEvent()->getRouteMatch()->getParam('id');
            $comments = $this->_commonDAO->executeQuery('COMMENT_GET_BY_POST_ID', array($postId));
            $this->_view->setVariable('comments', $comments);
        }
        $this->_view->setTemplate('application/post/template/comments.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getSubCommentsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if ($this->getRequest()->isGet())
        {
            $commentId = $this->getEvent()->getRouteMatch()->getParam('id');
            $comments = $this->_commonDAO->executeQuery('COMMENT_GET_BY_COMMENT_ID', array($commentId));
            $this->_view->setVariable('comments', $comments);
        }
        $this->_view->setTemplate('application/post/template/sub-comments.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function searchAction()
    {
        $this->init();

        $offset = (int) $this->params()->fromQuery('page', 1);
        $query = $this->params()->fromQuery('q', '');
        $this->_view->setVariable('q', $query);
        $query = $this->_utility->removeQuery($query);

        if(strlen($query) > 0)
        {
            $query .= '';
            $posts = $this->_commonDAO->executeQuery('POST_SEARCH_FULLTEXT', array($query));
            $posts = $this->_utility->getPagination($posts, Define::LIMIT, $offset);
            $this->_view->setVariable('posts', $posts);
        }

        $this->writeLog();
        return $this->_view;
    }
}