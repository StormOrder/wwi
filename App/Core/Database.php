<?php
namespace App\Core;

use App\Helpers\{ Utility, NavigationHelper as nav };
use App\Enum\MessageCodes;
use App\Enum\EUserTable;
use App\Enum\EArticleTable;

/**
 * Class db to connect with the database and send querys to database.
 * @since 30-03-2017
 * @version 1.0
 * @author R Haan
 */
class Database
{
	private static $_instance;

	private $connection;

    /**
     * db constructor.
     * setting up the database connection
     */
    public function __construct()
    {  
        try 
        {
            $path = "../config/conn.ini";
            if(nav::isOnLocalHost())
            {
                 $path = "../config/db.ini";
            }

            $this->connection = $this->CreateConnection($path);  
        }
        catch (\Exception $ex) 
        {
            $this->HandleUnexpectedException($ex);
        }      
       
    }

	/**
	 * Check if there already exists(singleton)
	 * @return Database object
	 */
	public static function getConnection()
    {
        //
        // If this class isn't instanced yet.
        //
        if (!self::$_instance)
        {
            //
            // Create an instance of this object.
            //
            self::$_instance = new self();
        }
        
        return self::$_instance;
	}

    /**
     * Creates Connection with Database.
     * @param string ini file path
     * @return connection
     */
    public function CreateConnection(string $path)
    {   
        try 
        {
            if ( file_exists($path) ) 
            {                
                //
                // Get the database information from the ini file.
                //
                $ini_file = parse_ini_file($path);
                $host     = $ini_file["Host"];
                $user     = $ini_file["User"];
                $pass     = $ini_file["Pass"];
                $dbname   = $ini_file["Db"];   

                //
                // Create an connection with the given information from that ini file.
                //
                $conn = new \mysqli($host, $user, $pass, $dbname);

                //
                // If there seems to be an error,
                //
                if ($conn->connect_error) 
                {
                    //
                    // Show the error and stop loading the website.
                    //
                    throw new SiteException(MessageCodes::rhadbe001, "Connection failed: {$conn->connect_error}");
                } 

                if(!$conn)
                {
                    throw new SiteException(MessageCodes::rhadbe002, "Something goes wrong mate!");
                }

                return $conn;          
            }
            else
            {
                throw new SiteException(MessageCodes::rhadbe003, "Config file {$path} not found.", 1);
                
            }
        }
        catch(\Exception $ex) {
            $this->HandleUnexpectedException($ex, false);
        }
    }

	/**
     * method to set the given query results into an array.
     * @param string $sql
     * @return array
     */
	public function queryResultsToArray( string $sql )
	{
        if($sql == null || $sql == "")
        {
            throw new SiteException(MessageCodes::rhadbe004, "The given SQL statement is empty.");
        }

        try
        {
            $sanitizedQuery = $this->sanitize($sql);
            //
            // Sanitize and get the needed information from the connected database.
            //
    		$result = $this->connection->query($sanitizedQuery);


            $array = [];
            $i = 0;

            //
            // set the given query results into an array.
            //
    		while ($row = $result->fetch_assoc()) 
    		{
    			$array[$i++] = $row;
    		}

    		return $array;          
        }
        catch (\Exception $ex) 
        {
            throw new SiteException(MessageCodes::rhadbe005, "The SQL statement could not be converted to an array.", $ex);
        } 
	}

    /**
     * method to delete a row from a datatable.
     * @param string $table
     * @param int $id
     * @return bool true when the query is executed successfully, false otherwise
     */
	public function Delete( string $table , string $id )
	{
        try
        {
            $query = "DELETE FROM `{$table}` 
                      WHERE `" . EUserTable::ID . "` = '{$id}'";
            //
            // If the row of the table can and is deleted.
            //
            return (bool)$this->connection->query($query);
          
        }
        catch (\Exception $ex) 
        {
            throw new SiteException(MessageCodes::rhadbe006, "The user with id '$id' could not be deleted", $ex);
        } 	        
	}

    /**
     * method to sanitize the given query.
     * @param object $conn
     * @param string $query
     * @return mixed
     */
	private function sanitize(string $query)
	{
        try
        {
	       return filter_var($this->connection->real_escape_string($query), FILTER_SANITIZE_STRING);          
        }
        catch (\Exception $ex) 
        {
            throw new SiteException(MessageCodes::rhadbe008, "The query could not be sanitized.", $ex);
        } 
	}

    /**
     * Creates a row in the given table name.
     * @param tableName, the table name.
     * @param data, multidimensial array with the cells to be updated and de values.
     * @return bool
     */
    public function Create(string $tableName, array $data)
    {
        $statement = ""; 
        try
        {
            foreach ($array as $key => $value) 
            { 
                if ($key == "Columns") 
                {
                    $statement .= "INSERT INTO `{$tableName}`"; 
                }
                elseif ($key == "Values")
                {
                    $statement .= "Values";
                }

                $statement .= "(";

                foreach ($value as $string) 
                {                    
                   $statement .= "`{$string}`";
                }
                
                $statement .= ")";
            }
            //
            // Insert a new article in datatable content
            //
            $query = $this->connection->query($statement);

            //
            // If the query above can be and is executed,
            //
            return $query;
          
        }
        catch (\Exception $ex) 
        {
            throw new SiteException(MessageCodes::rhadbe012, "The article could not be created.", $ex);
        } 
    }

    /**
     * Updates a table row.
     * @param tableName, the table name.
     * @param array, the data to update.
     * @return bool.
     */
    public function Update(string $tableName, array $array )
    {
        try
        {
            //
            // Update an article in datatable content
            //
            $statement = "
                UPDATE `{$tableName}` 
                SET";
                $i = 0;
                foreach ($array as $key => $value)
                {
                    if ($i == count($array) -1) 
                    {
                        $statement .= "WHERE ";
                    }

                    $statement .= "`{$key}` = '{$value}',";

                    if ($i < count($array) - 2) 
                    {
                        $statement .= ",";
                    }                                   
                    $i++;
                }                

            $query = $this->connection->query($statement);

            //
            // If the query above can be and is executed,
            // Then return true.
            // Else return false.
            //
            return $query;
          
        }
        catch (\Exception $ex) 
        {
            throw new SiteException(MessageCodes::rhadbe013, "The table '{$tableName}' could not be updated.", $ex);
        } 
    }
    
	/**
	 * Handles the unexpected exception
	 * @param ex. The exception.
	 * @param showErrorToUser. When true, show error to user, false otherwise.
	 */
	protected function HandleUnexpectedException(\Exception $ex, bool $showErrorToUser)
	{
		Utility::HandleUnexpectedException($ex, $showErrorToUser);
	}
}
?>