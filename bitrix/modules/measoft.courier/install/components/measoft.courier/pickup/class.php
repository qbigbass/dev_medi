<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main\Application,
    Bitrix\Main\Loader;

class CCustomAjax extends CBitrixComponent implements Controllerable
{

    private static $tableName = 'measoft_cities';

    /**
     * @return array
     */
    public function configureActions()
    {
        \Bitrix\Main\Loader::registerAutoLoadClasses(
            'measoft.courier',
            array(
                'Bitrix\Main\Engine\ActionFilter\CheckModulePermissions' => 'lib/checkmodulepermissions.php',
            )
        );
        return [
            'addCity' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new ActionFilter\CheckModulePermissions(),
                ],
                'postfilters' => []
            ],
            'delCityId' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new ActionFilter\CheckModulePermissions(),
                ],
                'postfilters' => []
            ],
            'getSenderCity' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new ActionFilter\CheckModulePermissions(),
                ],
                'postfilters' => []
            ],
            'getCoords' => [
                'prefilters' => [],
                'postfilters' => []
            ],
            'pvzSelected' => [
                'prefilters' => [],
                'postfilters' => []
            ]
        ];
    }

    function executeComponent()
    {
        $this->includeModules();
        $this->includeComponentTemplate();
    }


    protected function includeModules()
    {
        $success = true;

        if (!Loader::includeModule('measoft.courier'))
        {
            $success = false;
        }
        return $success;
    }

    /** Add new city on option page
     * @param $params
     * @return array
     */
    public function addCityAction($params) {

        $this->includeModules();

        $arrayParams = array();
        parse_str($params, $arrayParams);

        if (MeasoftEvents::isCp1251Site())
        {
            $arrayParams["NAME"] = iconv( 'UTF-8', 'CP1251', $arrayParams["NAME"]);
        }

        if ($arrayParams["MEASOFT_ID"] )
        {
            $DATA = [
                "MEASOFT_ID" => $arrayParams["MEASOFT_ID"],
                "NAME" => $arrayParams["NAME"]

            ];

            $connection = Application::getConnection();

            $success = $connection->add(
                self::$tableName,
                $DATA
            );
        }

        return [
            'success' => $success
        ];
    }

    /** Delete sender city on option page
     * @param $params
     * @return array
     */
    public function delCityIdAction($params)
    {
        $success = false;
        $arrayParams = array();
        parse_str($params, $arrayParams);
        if((int)$arrayParams['del_city_id'] > 0) {
            $connection = Application::getConnection();
            $sqlHelper = $connection->getSqlHelper();
            $string = "DELETE FROM `".self::$tableName."` WHERE `MEASOFT_ID`={$arrayParams['del_city_id']}";
            $string = $sqlHelper->forSql($string);
            $connection->queryExecute($string);
            $success = true;
        }
        return [
            'success' => $success
        ];
    }

    /** Get cities on request on option page
     * @param $search
     * @return array
     */
    public function getSenderCityAction($search)
    {
        $this->includeModules();
        $response = \MeasoftEvents::searchCity($search);
        return [
            $response
        ];
    }


    /**
     * @return false|array
     */
    public function getCoordsAction()
    {
        $session = Application::getInstance()->getSession();
        $data = $session->get('measoft');
        $response = json_encode($data["shop_coord"]);
        return $response;
    }

    /**
     * @param $pickupArr
     */
    public function pvzSelectedAction($pickupArr)
    {
        $pvzSelected = array(
            "pvz" => $pickupArr
        );
        $session = Application::getInstance()->getSession();
        $session->set('measoft', $pvzSelected);
    }

}