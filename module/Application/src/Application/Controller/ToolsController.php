<?php
namespace Application\Controller;

use Application\Constant\Define;
use Application\Constant\Key;
use Application\Model\postTable;

class ToolsController extends BaseController
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
        $tool = $this->_commonDAO->executeQueryFirst('TOOLS_GET_BY_ID', array($id));

        if($this->_request->isPost())
        {
            $outputPerFlowCell = $_POST[Key::OUTPUT_PER_FLOW_CELL];
            $outputPerFlowCellUnit = $_POST[Key::OUTPUT_PER_FLOW_CELL.'_unit'];

            $regionSizes = $_POST[Key::REGION_SIZE];
            $regionSizesUnit = $_POST[Key::REGION_SIZE.'_unit'];

            $coverages = $_POST[Key::COVERAGE];
            $coveragesUnit = $_POST[Key::COVERAGE.'_unit'];

            $numberOfSamples = $_POST[Key::NUMBER_OF_SAMPLES];
            $numberOfSamplesUnit = $_POST[Key::NUMBER_OF_SAMPLES.'_unit'];

            $index = isset($_COOKIE[Key::INDEX]) ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;

            setcookie(Key::OUTPUT_PER_FLOW_CELL, floatval($outputPerFlowCell), Define::EXPIRES + time(), '/');
            setcookie(Key::OUTPUT_PER_FLOW_CELL.'_unit', $outputPerFlowCellUnit, Define::EXPIRES + time(), '/');
            for($i = 0; $i < $index; $i++)
            {
                setcookie(Key::REGION_SIZE.'-'.($i+1), floatval($regionSizes[$i]), Define::EXPIRES + time(), '/');
                setcookie(Key::REGION_SIZE.'_unit-'.($i+1), $regionSizesUnit[$i], Define::EXPIRES + time(), '/');

                setcookie(Key::COVERAGE.'-'.($i+1), floatval($coverages[$i]), Define::EXPIRES + time(), '/');
                setcookie(Key::COVERAGE.'_unit-'.($i+1), $coveragesUnit[$i], Define::EXPIRES + time(), '/');

                setcookie(Key::NUMBER_OF_SAMPLES.'-'.($i+1), floatval($numberOfSamples[$i]), Define::EXPIRES + time(), '/');
                setcookie(Key::NUMBER_OF_SAMPLES.'_unit-'.($i+1), $numberOfSamplesUnit[$i], Define::EXPIRES + time(), '/');
            }
        }

        $this->_view->setVariable('tool', $tool);
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
            $tools = $this->_commonDAO->executeQuery('TOOLS_GET_ALL', array());
            $tools = $this->_utility->getPagination($tools, Define::LIMIT, $offset);
            $this->_view->setVariable('tools', $tools);
        }
        $this->_view->setTemplate('application/tools/template/items.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getItemAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $this->_view->setTemplate('application/tools/template/item.phtml');
        $this->writeLog();
        return $this->_view;
    }
    public function addInputsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $index = isset($_COOKIE[Key::INDEX]) ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;
        setcookie(Key::INDEX, ++$index, time() + Define::EXPIRES);

        $this->_view->setTemplate('application/tools/template/item.phtml');
        $this->writeLog();
        return $this->_view;
    }
    public function removeInputsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $index = isset($_COOKIE[Key::INDEX]) ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;
        if($index > Define::TOOLS_ITEM_SIZE)
        setcookie(Key::INDEX, --$index, time() + Define::EXPIRES);

        $this->_view->setTemplate('application/tools/template/item.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getInputsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $index = isset($_COOKIE[Key::INDEX]) ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;

        $regionSize = $this->getIOValue(Key::REGION_SIZE);
        $coverage = $this->getIOValue(Key::COVERAGE);
        $numberOfSamples = $this->getIOValue(Key::NUMBER_OF_SAMPLES);

        $outputRequired = $this->getIOValue(Key::OUTPUT_REQUIRED);
        $totalOutput = $this->getIOValue(Key::TOTAL_OUTPUT);
        $outputPerFlowCell = $this->getIOValue(Key::OUTPUT_PER_FLOW_CELL);

        $units = array(
            array(
                'text' => 'GB',
                'value' => Define::GB
            ),
            array(
                'text' => 'MB',
                'value' => Define::MB
            ),
            array(
                'text' => 'KB',
                'value' => Define::KB
            ),
            array(
                'text' => 'B',
                'value' => Define::B
            ),
        );

        $this->_view->setVariable('index', $index);
        $this->_view->setVariable('totalOutput', $totalOutput);
        $this->_view->setVariable('outputPerFlowCell', $outputPerFlowCell);
        $this->_view->setVariable('outputRequired', $outputRequired);
        $this->_view->setVariable('regionSize', $regionSize);
        $this->_view->setVariable('coverage', $coverage);
        $this->_view->setVariable('numberOfSamples', $numberOfSamples);

        $this->_view->setVariable('units', $units);

        $this->_view->setTemplate('application/tools/template/inputs.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getOutputsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $index = isset($_COOKIE[Key::INDEX]) ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;

        $totalOutput = $this->getIOValue(Key::TOTAL_OUTPUT);
        $outputPerFlowCell = $this->getIOValue(Key::OUTPUT_PER_FLOW_CELL);
        $outputRequired = $this->getIOValue(Key::OUTPUT_REQUIRED);
        $regionSize = $this->getIOValue(Key::REGION_SIZE);
        $coverage = $this->getIOValue(Key::COVERAGE);
        $numberOfSamples = $this->getIOValue(Key::NUMBER_OF_SAMPLES);

        $this->_view->setVariable('index', $index);
        $this->_view->setVariable('totalOutput', $totalOutput);
        $this->_view->setVariable('outputPerFlowCell', $outputPerFlowCell);
        $this->_view->setVariable('outputRequired', $outputRequired);
        $this->_view->setVariable('regionSize', $regionSize);
        $this->_view->setVariable('coverage', $coverage);
        $this->_view->setVariable('numberOfSamples', $numberOfSamples);

        $this->_view->setTemplate('application/tools/template/outputs.phtml');
        $this->writeLog();
        return $this->_view;
    }
    public function getSampleOutputsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $outputRequired = array(
            'key' => Key::OUTPUT_REQUIRED,
            'value' => $this->params()->fromQuery(Key::OUTPUT_REQUIRED)[Key::VALUE],
            'unit' => $this->params()->fromQuery(Key::OUTPUT_REQUIRED)[Key::UNIT],
        );
        $regionSize = array(
            'key' => Key::REGION_SIZE,
            'value' => $this->params()->fromQuery(Key::REGION_SIZE)[Key::VALUE],
            'unit' => $this->params()->fromQuery(Key::REGION_SIZE)[Key::UNIT],
        );
        $coverage = array(
            'key' => Key::COVERAGE,
            'value' => $this->params()->fromQuery(Key::COVERAGE)[Key::VALUE],
            'unit' => $this->params()->fromQuery(Key::COVERAGE)[Key::UNIT],
        );
        $numberOfSamples = array(
            'key' => Key::NUMBER_OF_SAMPLES,
            'value' => $this->params()->fromQuery(Key::NUMBER_OF_SAMPLES)[Key::VALUE],
            'unit' => $this->params()->fromQuery(Key::NUMBER_OF_SAMPLES)[Key::UNIT],
        );

        $index = $this->params()->fromQuery(Key::INDEX, 0);

        $this->_view->setVariable('index', $index);
        $this->_view->setVariable('outputRequired', $outputRequired);
        $this->_view->setVariable('regionSize', $regionSize);
        $this->_view->setVariable('coverage', $coverage);
        $this->_view->setVariable('numberOfSamples', $numberOfSamples);

        $this->_view->setTemplate('application/tools/template/sample-outputs.phtml');
        $this->writeLog();
        return $this->_view;
    }
    public function getSampleOutputAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        $key = $this->params()->fromQuery(Key::KEY, false);
        $value = $this->params()->fromQuery(Key::VALUE, 0);
        $unit = intval($this->params()->fromQuery(Key::UNIT, Define::B));
        $index = $this->params()->fromQuery(Key::INDEX, 0);
        $required = $this->params()->fromQuery(Key::REQUIRED, false);

        $this->_view->setVariable(Key::KEY, $key);
        $this->_view->setVariable(Key::VALUE, $value);
        $this->_view->setVariable(Key::UNIT, $unit);
        $this->_view->setVariable(Key::INDEX, $index);
        $this->_view->setVariable(Key::REQUIRED, $required);

        $this->_view->setTemplate('application/tools/template/sample-output.phtml');
        $this->writeLog();
        return $this->_view;
    }

    private function getIOValue($key)
    {
        $index = isset($_COOKIE[Key::INDEX]) ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;

        $result = array(
            'key' => $key
        );
        switch($key)
        {
            case Key::OUTPUT_PER_FLOW_CELL:
                $result['value'] = isset($_COOKIE[$key]) == true ? $_COOKIE[$key] : 0;
                $result['unit'] = isset($_COOKIE[$key.'_unit']) == true ? $_COOKIE[$key.'_unit'] : Define::B;
                break;
            case Key::OUTPUT_REQUIRED:
                for ($i = 1; $i <= $index; $i++)
                {
                    $vu = $this->calOutputRequired($i);
                    $result['value'][] = $vu['value'];
                    $result['unit'][] = $vu['unit'];
                }
                break;
            case Key::TOTAL_OUTPUT:
                $vu = $this->calTotalOutput();
                $result['value'] = $vu['value'];
                $result['unit'] = $vu['unit'];
                break;
            case Key::REGION_SIZE:
            case Key::COVERAGE:
            case Key::NUMBER_OF_SAMPLES:
                for ($i = 1; $i <= $index; $i++)
                {
                    if($i == $index && $key == Key::NUMBER_OF_SAMPLES)
                        $vu = $this->calNumberOfSamplesN();

                    else
                    {
                        $vu['value'] = isset($_COOKIE[$key.'-'.$i]) == true ? $_COOKIE[$key.'-'.$i] : 0;
                        $vu['unit'] = isset($_COOKIE[$key.'_unit-'.$i]) == true ? $_COOKIE[$key.'_unit-'.$i] : Define::B;
                    }

                    $result['value'][] = $vu['value'];
                    $result['unit'][] = $vu['unit'];
                }
                break;
        }

        return $result;
    }
    private function calTotalOutput()
    {
        $vu = array('value' => 0, 'unit' => Define::B);
        $v = 0;
        $index = isset($_COOKIE[Key::INDEX])==true ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;
        for($i = 1; $i <= $index-1; $i++)
        {
            $vu = $this->calOutputRequired($i);
            if($vu['value'] == 0)
                return $vu;
            $v += $vu['value']*$vu['unit'];
        }

        if(!isset($_COOKIE[Key::OUTPUT_PER_FLOW_CELL]))
            return array('value' => 0, 'unit' => Define::B);

        $uO = isset($_COOKIE[Key::OUTPUT_PER_FLOW_CELL.'_unit']) == true ? $_COOKIE[Key::OUTPUT_PER_FLOW_CELL.'_unit'] : Define::B;
        $vu['value'] = $_COOKIE[Key::OUTPUT_PER_FLOW_CELL]*$uO - $v;

        return $vu;
    }
    private function calOutputRequired($i)
    {
        $vu = array(
            'value' => 0,
            'unit' => Define::B
        );

        if(isset($_COOKIE[Key::REGION_SIZE.'-'.$i])
        && isset($_COOKIE[Key::COVERAGE.'-'.$i])
        && isset($_COOKIE[Key::NUMBER_OF_SAMPLES.'-'.$i]))
        {
            $uR = isset($_COOKIE[Key::REGION_SIZE.'_unit-'.$i]) == true ? $_COOKIE[Key::REGION_SIZE.'_unit-'.$i] : Define::B;
            $uC = isset($_COOKIE[Key::COVERAGE.'_unit-'.$i]) == true ? $_COOKIE[Key::COVERAGE.'_unit-'.$i] : Define::B;
            $uN = isset($_COOKIE[Key::NUMBER_OF_SAMPLES.'_unit-'.$i]) == true ? $_COOKIE[Key::NUMBER_OF_SAMPLES.'_unit-'.$i] : Define::B;
            $v = $_COOKIE[Key::REGION_SIZE.'-'.$i] * $_COOKIE[Key::COVERAGE.'-'.$i] * $_COOKIE[Key::NUMBER_OF_SAMPLES.'-'.$i] * $uR *$uC *$uN;
            $vu['value'] = $v;
        }

        return $vu;
    }
    private function calNumberOfSamplesN()
    {
        $vu = array('value' => 0, 'unit' => Define::B);
        $index = isset($_COOKIE[Key::INDEX])==true ? $_COOKIE[Key::INDEX] : Define::TOOLS_ITEM_SIZE;

        if(isset($_COOKIE[Key::REGION_SIZE.'-'.$index]) && $_COOKIE[Key::REGION_SIZE.'-'.$index] != 0
            && isset($_COOKIE[Key::COVERAGE.'-'.$index]) && $_COOKIE[Key::COVERAGE.'-'.$index] != 0)
        {
            $rU = isset($_COOKIE[Key::REGION_SIZE.'_unit-'.$index])==true ? $_COOKIE[Key::REGION_SIZE.'_unit-'.$index] : Define::B;
            $cU = isset($_COOKIE[Key::COVERAGE.'_unit-'.$index])==true ? $_COOKIE[Key::COVERAGE.'_unit-'.$index] : Define::B;
            $output = $this->calTotalOutput();
            $v = $output['value']*$output['unit'] / ($_COOKIE[Key::REGION_SIZE.'-'.$index] * $rU * $_COOKIE[Key::COVERAGE.'-'.$index] * $cU);
            $vu['value'] = $v;
        }

        return $vu;
    }
}