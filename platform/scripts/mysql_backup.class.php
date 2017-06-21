<?php 

/**
 * The Backup_Database class
 */
class Backup_Database {
    /**
     * Host where database is located
     */
    var $host = '';

    /**
     * Username used to connect to database
     */
    var $username = '';

    /**
     * Password used to connect to database
     */
    var $passwd = '';

    /**
     * Database to backup
     */
    var $dbName = '';

    /**
     * Database charset
     */
    var $charset = '';

    /**
     * Constructor initializes database
     */
    function Backup_Database($host, $username, $passwd, $dbName, $charset = 'utf8')
    {
        $this->host     = $host;
        $this->username = $username;
        $this->passwd   = $passwd;
        $this->dbName   = $dbName;
        $this->charset  = $charset;

        $this->initializeDatabase();
    }

    protected function initializeDatabase()
    {
		$this->db = new PDO("mysql:host=$this->host;dbname=$this->dbName;charset=$this->charset", $this->username, $this->passwd );
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Backup the whole database or just some tables
     * Use '*' for whole database or 'table1 table2 table3...'
     * @param string $tables
     */
    public function backupTables($tables = '*', $outputDir = '.')
    {
        try
        {
            /**
            * Tables to export
            */
            if($tables == '*')
            {
                $tables = array();
                foreach( $this->db->query('SHOW TABLES') as $row )
                {
                    $tables[] = $row[0];
                }
            }
            else
            {
                $tables = is_array($tables) ? $tables : explode(',',$tables);
            }

            $sql = '';
			$sql .= 'CREATE DATABASE IF NOT EXISTS `'.$this->dbName."`;".PHP_EOL;
            $sql .= 'USE `'.$this->dbName."`;".PHP_EOL;

            /**
            * Iterate tables
            */
            foreach($tables as $table)
            {
				$result = $this->db->query( "SELECT * FROM `$table`");
				$numFields = $result->columnCount();

                $sql .= "DROP TABLE IF EXISTS `$table`;".PHP_EOL;
				$result2 = $this->db->query( "SHOW CREATE TABLE `$table`");
				$row2 = $result2->fetch();
                $sql.= str_replace(array(PHP_EOL, "\n", "\r\n"), ' ',$row2[1]).";".PHP_EOL;

                for ($i = 0; $i < $numFields; $i++) 
                {
                    while( $row = $result->fetch() )
                    {
                        $sql .= 'INSERT INTO `'.$table.'` VALUES(';
                        for($j=0; $j<$numFields; $j++) 
                        {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = preg_replace( '/\n/' , "\\n" , $row[$j] );
                            if (isset($row[$j]))
                            {
							//	$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($row[$j]) : mysql_escape_string($row[$j]);
                               $sql .= '"'. $row[$j].'"';
                            }
                            else
                            {
                                $sql.= '""';
                            }

                            if ($j < ($numFields-1))
                            {
                                $sql .= ',';
                            }
                        }

                        $sql.= ");".PHP_EOL;
                    }
                }

                $sql.= PHP_EOL;
            }
        }
        catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
        
        if($outputDir == false){
	        return $sql;
        } else {
	        return $this->saveFile($sql, $outputDir);
	    }
    }

    /**
     * Save SQL to file
     * @param string $sql
     */
    protected function saveFile(&$sql, $outputDir = '.')
    {
        if (!$sql) return false;

        try
        {
            $handle = fopen($outputDir.'/db-backup-'.$this->dbName.'-'.date("Ymd-His", time()).'.sql','w+');
            fwrite($handle, $sql);
            fclose($handle);
        }
        catch (Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }

        return true;
    }
}
?>