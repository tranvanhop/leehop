<?php
namespace Generator\Controller;

use Faker\Factory;
use Generator\Constant\Define;
use Generator\Constant\Key;

class IndexController extends BaseController
{
    protected $_faker;

    public function __construct()
    {
        parent::__construct();
        $this->_faker = Factory::create();
    }
    public function indexAction()
    {
        $this->init();
        $this->writeLog();
        return $this->_view;
    }

    public function addUserAction()
    {
        $this->init();

        if($this->_request->isGet())
        {
            $n = isset($_GET[Key::N]) ? $_GET[Key::N] : Define::N;
            $data = array();

            echo 'Time start : '.date('H:i:s');
            for($i = 0; $i < $n; $i++)
            {
                $data[] = array(
                    'first_name' => $this->_faker->firstName,
                    'last_name' => $this->_faker->lastName,
                    'email' => $this->_faker->email,
                    'password' => $this->_faker->sha1,
                    'phone' => $this->_faker->phoneNumber,
                    'avatar' => null,
                    'create_date' => date('Y-m-d H:i:s'),
                    'ip_address' => $this->_faker->ipv4,
                    'user_agent' => $this->_faker->userAgent
                );
            }
            $this->pdoMultiInsert('user', $data, $this->_conn);
            echo ' - Time end : '.date('H:i:s');
        }

        $this->writeLog();
        return false;
    }

    public function addPostAction()
    {
        $this->init();

        if($this->_request->isGet())
        {
            $n = isset($_GET[Key::N]) ? $_GET[Key::N] : Define::N;
            $data = array();
            $categories = $this->_commonDAO->executeQuery('CATEGORIES_GET_ALL', array());
            $tags = 'Metronic, Keenthemes, UI Design';

            echo 'Time start : '.date('H:i:s');
            for($i = 0; $i < $n; $i++)
            {
                $summary = $this->_faker->text(400);
                $data[] = array
                (
                    'category_id' => $categories[$this->_faker->numberBetween(0, count($categories)-1)]['id'],
                    'title' => $this->_faker->jobTitle,
                    'summary' => '<p>'.$summary.'</p>',
                    'description' => '<p>'.$summary.'</p>'.'<p>'.$this->_faker->text(2000).'</p>',
                    'thumbnail' => '/theme/assets/frontend/pages/img/works/img'.$this->_faker->numberBetween(1, 6).'.jpg',
                    'create_date' => date('Y-m-d H:i:s'),
                    'create_by' => 1,
                    'tags' => $tags
                );
            }
            $this->pdoMultiInsert('post', $data, $this->_conn);
            echo ' - Time end : '.date('H:i:s');
        }

        $this->writeLog();
        return false;
    }
}