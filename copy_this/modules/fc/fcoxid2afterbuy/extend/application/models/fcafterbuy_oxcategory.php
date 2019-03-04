<?php
/**
 * Created by PhpStorm.
 * User: andrefatchip
 */

class fcafterbuy_oxcategory extends fcafterbuy_oxcategory_parent
{
    /**
     * Overloading load method for appending additional table
     *
     * @param $oxID
     * @return mixed
     */
    public function load($oxID) {
        $mReturn = parent::load($oxID);

        if ($mReturn) {
            $this->_fcAddCustomFieldsToObject($oxID);
        }

        return $mReturn;
    }

    /**
     * Overloading save method to assign afterbuy values to subtable
     *
     * @return mixed
     */
    public function save() {
        $blRet = parent::save();
        $this->fcSaveAfterbuyParams();

        return $blRet;
    }

    /**
     * Adds fields of custom table too current object
     *
     * @param string $sOxid
     * @return void
     */
    protected function _fcAddCustomFieldsToObject($sOxid) {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $aFields = array('FCAFTERBUY_CATALOGID');
        $sFields = implode(",", $aFields);

        $sQuery = "
            SELECT
                {$sFields}
            FROM
                oxcategories_afterbuy
            WHERE OXID = '{$sOxid}'
        ";
        $aRow = $oDb->getRow($sQuery);
        if (!is_array($aRow) || count($aRow)==0) {
            $this->_fcFillFields($aFields);
            return;
        }

        foreach ($aRow as $sDbField=>$sValue) {
            $sDbField = strtolower($sDbField);
            $sField = "oxcategories__".$sDbField;
            $this->$sField = new oxField($sValue);
        }
    }

    /**
     * Fills empty values if row has not been found. Shall
     * prevent usage of former table values
     *
     * @param $aFields
     * @return void
     */
    protected function _fcFillFields($aFields)
    {
        // fill values anyway due to possibly
        // resisting values in former structure
        foreach ($aFields as $sDbField) {
            $sDbField = strtolower($sDbField);
            $sField = "oxcategories__".$sDbField;
            $this->$sField = new oxField('');
        }

    }

    /**
     * Save afterbuy params
     *
     * @param void
     * @return void
     */
    public function fcSaveAfterbuyParams() {
        $oAfterbuyDb = oxNew('fco2adatabase');
        $sOxid = $this->getId();

        $aAfterbuyParams = array(
            'FCAFTERBUY_CATALOGID' => $this->oxcategories__fcafterbuy_catalogid->value,
        );

        $oAfterbuyDb->fcSaveAfterbuyParams(
            'oxcategories_afterbuy',
            'oxcategories',
            $sOxid,
            $aAfterbuyParams
        );
    }

}