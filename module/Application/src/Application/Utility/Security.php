<?php

namespace Application\Utility;

use Zend\Crypt\Password\Bcrypt;

class Security
{

    public $salt = 'aUJGgadjasdgdj';

    public $method = 'sha1';

    public function __construct($method = null, $salt = null)
    {
        if (! is_null($method)) {
            $this->method = $method;
        }

        if(! is_null($salt))
        {
            $this->salt = $salt;
        }
    }

    public function create($password)
    {
        if ($this->method == 'md5') {
            return md5($this->salt . $password);
        } elseif ($this->method == 'sha1') {
            return sha1($this->salt . $password);
        } elseif ($this->method == 'bcrypt') {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            return $bcrypt->create($password);
        }
    }

    public function verify($password, $hash)
    {
        if ($this->method == 'md5') {
            return $hash == md5($this->salt . $password);
        } elseif ($this->method == 'sha1') {
            return $hash == sha1($this->salt . $password);
        } elseif ($this->method == 'bcrypt') {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            return $bcrypt->verify($password, $hash);
        }
    }
}
