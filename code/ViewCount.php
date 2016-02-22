<?php
class ViewCount extends DataObject
{
    
    public static $db = array(
        'Count' => 'Int',
        'RecordID' => 'Int',
        'RecordClass' => 'Varchar(255)'
    );

    public static $indexes = array(
        'RecordIDRecordClass' => array(
            'type' => 'index',
            'value' => 'RecordID,RecordClass'
        ),
        'RecordIDRecordClassUnique' => array(
            'type' => 'unique',
            'value' => 'RecordID,RecordClass'
        )
    );

    /**
     * @return DataObject
     */
    public function getRecord()
    {
        $class = $this->RecordClass;
        return $class::get()->byID($this->RecordID)->First();
    }
}
