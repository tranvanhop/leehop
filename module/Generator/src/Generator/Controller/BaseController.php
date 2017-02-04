<?php
namespace Generator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BaseController extends AbstractActionController
{
    protected $_conn;
    protected $_folder;
    protected $_fileNameLog;
    protected $_log;
    protected $_view;
    protected $_commonDAO;
    protected $_request;
    protected $_action;
    protected $_controller;

    public function __construct()
    {
        $this->_folder = $_SERVER['DOCUMENT_ROOT'] . '/logs/';
        $this->_fileNameLog = $this->_folder . 'LOG-'.date('Y-m-d').'.txt';

        $this->_log = '['. $_SERVER['REMOTE_ADDR'] . '] ['. date('d/m/Y H:i:s'). '] ['.$_SERVER['HTTP_USER_AGENT'].'] ';

        $this->_view = new ViewModel();
    }
    public function getBaseUrl()
    {
        $uri = $this->getRequest()->getUri();
        return sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
    }
    public function init()
    {
        $db = $this->getServiceLocator()->get('Config')['db'];
        $this->_conn = new \PDO($db['dsn'], $db['username'], $db['password'], $db['driver_options']);

        $this->_commonDAO = $this->getServiceLocator()->get('CommonDAO');
        $this->_request = $this->getRequest();

        $this->_action = $this->params('action');
        list($module, $controller, $this->_controller) = explode("\\", $this->params('controller'));

        $method = $this->_request->getMethod();
        $this->_log .='['.$this->_controller.'Controller] ['.$this->_action.'Action] [Method : '.$method.'] ';

        switch($method)
        {
            case 'GET':
                if($_GET)
                    $this->_log .= '[Params : '.json_encode($_GET).'] ';
                break;
            case 'POST':
                if($_POST)
                    $this->_log .= '[Params : '.json_encode($_POST).'] ';
                break;
        }
    }
    public function writeLog()
    {
        exec('echo "'.$this->_log.'" >> '.$this->_fileNameLog, $output);
    }

    public function getFieldsFromTable($tableName, $pdoObject)
    {
        $q = $pdoObject->prepare("DESCRIBE ".$tableName);
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function pdoMultiInsert($tableName, $data, $pdoObject){

        //Will contain SQL snippets.
        $rowsSQL = array();

        //Will contain the values that we need to bind.
        $toBind = array();

        //Get a list of column names to use in the SQL statement.
        $columnNames = array_keys($data[0]);

        //Loop through our $data array.
        foreach($data as $arrayIndex => $row){
            $params = array();
            foreach($row as $columnName => $columnValue){
                $param = ":" . $columnName . $arrayIndex;
                $params[] = $param;
                $toBind[$param] = $columnValue;
            }
            $rowsSQL[] = "(" . implode(", ", $params) . ")";
        }

        //Construct our SQL statement
        $sql = "INSERT INTO $tableName (" . implode(", ", $columnNames) . ") VALUES " . implode(", ", $rowsSQL);

        //Prepare our PDO statement.
        $pdoStatement = $pdoObject->prepare($sql);

        //Bind our values.
        foreach($toBind as $param => $val){
            $pdoStatement->bindValue($param, $val);
        }

        //Execute our statement (i.e. insert the data).
        return $pdoStatement->execute();
    }
}