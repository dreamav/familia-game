<?php
class c_database {
//__________________INTERFACE____________________
public function sethost($host)
{$this->lasterror='';$this->host=$host;}
public function setuser($user)
{$this->lasterror='';$this->user=$user;}
public function setpassword($password)
{$this->lasterror='';$this->password=$password;}
public function setcharset($charset)
{$this->lasterror='';$this->charset=$charset;}
public function setdatabase($db)
{$this->lasterror='';$this->databasename=$db;}
public function free()
{
  if(is_resource($this->sqlresult))
  mysql_free_result($this->sqlresult);}
public function connect()
{$this->lasterror=''; $this->_connect();}
public function query_exec()
{$this->lasterror='';
$args=func_get_args();
$this->_query_exec($args);}
public function query_direct($query)
{$this->querysCounter++;
$this->sqlresult=mysql_query($query,$this->connectionID);
if(!$this->sqlresult)
    $this->lasterror=mysql_error($this->connectionID);
}
public function GetLastID()
{$this->lasterror=''; $ret=mysql_insert_id($this->connectionID); return $ret;}
public function iniSet()
{ $this->LoadfromINIFile();}
public function escapeString($str)
{return mysql_real_escape_string($str,$this->connectionID);}
public function fetch($id="",$fetchtype=1)
{
  $result=array();
  // echo $this->lastquery."<br>";
  while($row=mysql_fetch_array($this->sqlresult,$fetchtype)){
    if($id){
       if(isset($row[$id])){
         $result[$row[$id]]=$row;
       }else{
         $result[]=$row;
       }
    }else{
       $result[]=$row;
    }

  }
  return $result;
}
public function fetchsimple($id="",$fetchtype=2)
{
  $result=array();
  while($row=mysql_fetch_array($this->sqlresult,$fetchtype)){
    if($id){
       if(isset($row[$id])){
         $result[$row[$id]]=$row;
       }else{
         $result[]=$row;
       }
    }else{
       $result[]=$row[0];
    }
  }
  return $result;
}
public function getfoundRows()
{
  return $this->foundrows;
}
public $sqlresult;
public $LastQueryStatus;
public $affectedrows;
//_______________________________________________
private $host;
private $user;
private $password;
private $databasename;
private $charset;
private $connectionID;
private $magicquotes;
private $foundrows;
private $querysCounter;
public $lastquery;
public $lasterror;
public $DEBUG;
public $ini;
protected $queries=array();

private function LoadfromINIFile($inifile='')
{$this->lasterror='';
 if($inifile){
     $ini=new c_iniFiles($inifile);
     $this->ini= $ini;
 }
 if ($this->ini->error)
     $this->lasterror=$this->ini->error;
 else
 {
        $this->host="localhost";
        $this->user="familia";
        $this->password="5C2h3A1j";
        $this->databasename="familia";
        $this->charset="utf8";
        $this->_connect();
 }
}
private function _connect()
{
$this->lasterror='';                    //СТАРЫЙ ИНТЕРФЕЙС   MYSQL
 $this->connectionID=@mysql_connect($this->host,$this->user,$this->password);
  if (! $this->connectionID){
      $this->lasterror="Can't connect to database ($this->host,$this->user,$this->password";
      trigger_error($this->lasterror);
  }else{
    $f=mysql_select_db($this->databasename,$this->connectionID);
        if(!$f){
           $this->lasterror=mysql_error($this->connectionID);
        }
        if (($f) and ($this->charset)){
           $this->query_exec('SET NAMES ?',$this->charset);
        }
  }

}
// -------QUERY FUNCTIONS ---------------------------------
private function _query_exec($args)
{ $this->querysCounter++;
  $this->LastQueryStatus=false;
  $query=$this->escape($args);
  //$res=mysql_query('USE '.$this->databasename,$this->connectionID);
  //$this->lasterror=mysql_error($this->connectionID);
  $res=mysql_query($query,$this->connectionID);
  if($this->DEBUG){
     $this->logQuery($query);
  }
  $this->foundrows=false;
  if(mysql_error($this->connectionID)){
      $this->LastQueryStatus=true;
      //trigger_error(mysql_error($this->connectionID)."\nLast query: ".$query,256);
  }
  if(!$res){
     $this->lasterror=mysql_error($this->connectionID);
     $this->LastQueryStatus=true;
  }else{
     $this->sqlresult=$res;
     $this->affectedrows=mysql_affected_rows($this->connectionID);
     if(strpos($query,'SQL_CALC_FOUND_ROWS')){
          $found=mysql_query('SELECT FOUND_ROWS()');
          $this->foundrows=mysql_result($found,0);
     }
  }
  $this->lastquery=$query;
}

//-----------QUERY FUNCTIONS END---------------------------
private function escape($args)
{$ret='';
 //$args=str_replace("\r","",$args);
 //$args=str_replace("\n","",$args);
 //$args=str_replace("\t","",$args);
 $query=array_shift($args); //$query -
 $query=explode('?',$query);
 $args=$this->UnpackArgs($args);
 foreach($query as $i=>$Q)
  {
   if (isset($args[$i])) //Null если count(Query)==count($args)+1
     {
       if (!is_numeric($args[$i]) )
         {
           if(is_string($args[$i]))
            {
             $args[$i]=urldecode($args[$i]);
             $charset= $this->charset=='utf8' ? 'UTF-8' : $this->charset;
             $args[$i]=htmlspecialchars($args[$i],ENT_QUOTES, $charset);
             $args[$i]=mysql_real_escape_string($args[$i],$this->connectionID);
             if(strpos($args[$i],'$')===false)
                $args[$i]='"'.$args[$i].'"';
             else
                $args[$i]=str_replace('$','',$args[$i]);
            }
         }else{
              $args[$i]='"'.$args[$i].'"';
         }
       $ret=$ret.$query[$i].$args[$i];
     }
    else
       $ret=$ret.$query[$i];
  }
$ret=str_replace(array("\n","\r"),array(''),$ret);
return $ret;
}
private function UnpackArgs($arr)
{
 $result=array();
 foreach($arr as $i=>$el)
   {
     if(!is_array($el)){
       if(!is_null($el))
          $result[]=$el;
     }
     else
       $result=array_merge_recursive($result,$this->UnpackArgs($el));
   }
 return $result;
}
private function logQuery($query)
 {
   $errstatus=mysql_errno($this->connectionID) ? 1 : 0;
   $this->queries[]=array($query,$errstatus);
 }
//_________________________________________________________________________
function __construct($inifile='') {
	$this->querysCounter=0;
	if ( $inifile !== '' )
		$this->LoadfromINIFile($inifile);
	if ( get_magic_quotes_gpc() ) {
		$this->magicquotes=1;
	} else {
		$this->magicquotes=0;
	}
}
function __destruct()
{
  if($this->DEBUG){
     //print "<!-- ".$this->querysCounter." queries -->";
     $path=dirname(__FILE__)."/../debug/".date('Y-m-d')."queries.log";
     $f=@fopen($path,'a+');
     if($f){
        $date=date('H:i:s');
        $log="\n\n{$date}:\n";

        foreach($this->queries as $q){
           $log.= $q[1]>0 ? $q[0] : "error at ".$q[0]."\n";
        }
        fwrite($f,$log);
        fclose($f);
     }
  }
}
}//class

class dataBase {
	private static $DB;
	public static function Create( $ini="" ) {
		if ( is_object($ini) ) {
			$DB = new c_database();
			$DB->ini=$ini;
			$DB->iniSet();
			//$DB->connect($new);
			self::$DB=$DB;
		} else {
			if ( self::$DB ) {
				$DB=self::$DB;
			} else {
				$DB = new c_database($ini);
				self::$DB=$DB;
			}
		}
		return $DB;
	}
}

?>
