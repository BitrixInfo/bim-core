<?php

namespace Bim\Db\Lib;

/**
 * Class IblockGen
 * класс для генерации кода изменений в инфоблоке:
 */
class IblockGen extends \Bim\Db\Lib\CodeGenerator
{

    public function __construct(){
        \CModule::IncludeModule('iblock');
    }

    /**
     * метод для генерации кода добавления нового  инфоблока
     * @param array $IblockCode
     * @return mixed
     * @internal param array $params
     */
    public function generateAddCode($IblockCode)
    {
        $Iblock = new \CIBlock();
        $return = array();
        $dbIblock = $Iblock->GetList(array(), array('CODE' => $IblockCode));
        if ($arIblock = $dbIblock->Fetch()) {
            $arIblock['GROUP_ID'] = \CIBlock::GetGroupPermissions($arIblock['ID']);
            $arIblock['FIELDS'] = \CIBlock::GetFields($arIblock['ID']);
            unset($arIblock['ID']);
            if ($return[] = $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Add', array($arIblock))) {
                $IblockProperty = new \CIBlockProperty();
                $dbIblockProperty = $IblockProperty->GetList(array(), array('IBLOCK_CODE' => $arIblock['CODE']));
                while ($arIblockProperty = $dbIblockProperty->Fetch()) {
                    unset($arIblockProperty['ID']);
                    $dbPropertyValues = \CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID" => $arIblockProperty['IBLOCK_ID'], "CODE" => $arIblockProperty['CODE']));
                    while ($arPropertyValues = $dbPropertyValues->Fetch())
                        unset($arPropertyValues['PROPERTY_ID']);
                    $arIblockProperty['VALUES'][$arPropertyValues['ID']] = $arPropertyValues;
                    $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Add', array($arIblockProperty));
                }
                return implode(PHP_EOL, $return);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * метод для генерации кода обновления инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode( $params )
    {
        $this->checkParams( $params );
        $code = false;
        foreach( $this->ownerItemDbData as $iblockData  ){
            $updateFields = $iblockData;
            unset( $updateFields['ID'] );
            $updateFields['FIELDS'] = \CIBlock::GetFields( $iblockData['ID'] );
            $updateFields['GROUP_ID'] = \CIBlock::GetGroupPermissions( $iblockData['ID'] );
            $code = $code . $this->buildCode('Bim\Db\Iblock\IblockIntegrate', 'Update', array( $updateFields['CODE'], $updateFields ) ) .PHP_EOL.PHP_EOL;
        }
        return $code;
    }

    /**
     * метод для генерации кода удаления  инфоблока
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode( $params )
    {
        $this->checkParams( $params );
        $code = false;
        foreach( $this->ownerItemDbData as $iblockData  ){
            $code =  $this->buildCode('Bim\Db\Iblock\IblockIntegrate', 'Delete', array( $iblockData['CODE'] ) );
        }
        return $code;
    }


    /**
     * абстрактный метод проверки передаваемых параметров
     * @param $params array
     * @return mixed
     */
    public function checkParams($params)
    {
        // TODO: Implement checkParams() method.
    }
}