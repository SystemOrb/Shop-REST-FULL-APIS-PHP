<?php
require_once 'config.php';
class dbDriver
/*
 * Clase que genera las conexiones dinamicamente
 */
{
    public function __construct() { 
    }
    public function setPDO()
    {
        return new PDO(PDO_HOSTNAME, PDO_USER, PDO_PASS);
    }
    public function setPDOConfig($PDO_CONSTRUCTOR)
    {
        $obj = $PDO_CONSTRUCTOR;
        $obj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $obj->exec(PDO_CHAR);
    }
    public function setBBDD($object){
        $this->BBDD = $object;
    }
    public function getBBDD(){
        return $this->BBDD;
    }
    public function selectDriver($condition,$bbdd, $PDO_port)
    {
        if($condition!=null){
           return $PDO_port->prepare("SELECT * FROM {$bbdd} WHERE {$condition}");
        }else{
           return $PDO_port->prepare("SELECT * FROM {$bbdd}");
            
            }       
    }
    public function scapeCharts($value)
    {
        return htmlentities(addslashes($value));
    }
    public function countDriver($condition,$bbdd,$PDO_port)
    {
        if($condition!=null)
        {
           return $PDO_port->prepare("SELECT COUNT(*) as index FROM {$bbdd} WHERE {$condition}");
        }else{
            return $PDO_port->prepare("SELECT COUNT(*) as index FROM {$bbdd}");
        }
    }
    public function countDriverByGroup($condition,$bbdd,$PDO_port,$field,$group)
    {
        if($condition!=null)
        {
            return $PDO_port->prepare("SELECT SUM({$field} as index FROM {$bbdd} WHERE {$condition} GROUP BY {$group} ASC");
        }else{
            return $PDO_port->prepare("SELECT SUM{$field} as index FROM {$bbdd} GROUP BY {$group} ASC ");
        }
    }
    public function sumDriver($condition,$bbdd,$PDO_port,$field)
    {
        if($condition!=null)
        {
            return $PDO_port->prepare("SELECT SUM({$field}) AS total FROM {$bbdd} WHERE {$condition}");
        }else{
            return $PDO_port->prepare("SELECT SUM({$field}) AS total FROM {$bbdd}");
        }
    }
    
    public function insertDriver($condition,$bbdd,$PDO_port,$fields)
    {
        //$this->setobjectPDO($this->BBDD->prepare("INSERT INTO {$bbdd} VALUES ({$condition})"));
        return $PDO_port->prepare("INSERT INTO {$bbdd}({$fields}) VALUES({$condition})");
    }
    public function updateDriver($condition,$bbdd,$PDO_port,$fields){
        return $PDO_port->prepare("UPDATE {$bbdd} SET {$fields} WHERE {$condition}");
    }
    public function deleteDriver($condition,$bbdd,$PDO_port)
    {
        if($condition!='')
        {
            return $PDO_port->prepare("DELETE FROM {$bbdd} WHERE {$condition}");
        }else{
            return $PDO_port->prepare("DELETE FROM {$bbdd}");
        }
    }
   public function runDriver($sentence,$PDO_OBJECT)
   {
       if($sentence!=null)
       {
           $PDO_OBJECT->execute($sentence);
       }else{
           $PDO_OBJECT->execute();
       }
   }
   public function cicleDriver($PDO_OBJECT){
       foreach($PDO_OBJECT->fetchAll(PDO::FETCH_OBJ) as $array){
           return $array;
       }
   }
   public function fetchDriver($PDO_OBJECTS)
   {
       return $PDO_OBJECTS->fetchAll(PDO::FETCH_OBJ);
   }
   public function verifyDriver($PDO_OBJECT)
   {
       if($PDO_OBJECT->rowCount()!=0)
       {
           return true;
       }else{
           return false;
       }
   }
    public function setobjectPDO($PDO_OBJECT)
    {
        return $PDO_OBJECT;

    }
    public function getObjectPDO()
    {
        return $this->PDO;
    }
    public function setQuery($arrayResponse)
    {
        $this->query = $arrayResponse;
    }
    public function getQuery()
    {
        return $this->query;
    }
    protected $BBDD;
    protected $query;
    protected $PDO;
}
