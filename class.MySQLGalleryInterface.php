<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//--How to use

//--Add this class to what you want to use it with and create an object
//      include ('class.MySQLHandler.php');
//      $MySQLHandler = new MySQLHandler();

//--loads data from the specified table
//      $MySQLHandler->getTable($table,$flags)

//--loads entry from the specified table
//      $MySQLHandler->getEntry($table,$entryKey)

//--Adds a new entry to a table
//      $MySQLHandler->addEntry($table,$data,$flags)

//--Modifies an entry from a table
//      $MySQLHandler->uptadeEntry($table,$entryKey,$data,$flags)

//--Deletes an entry from a table
//      $MySQLHandler->deleteEntry($table,$entryKey)

//--Accepts a raw SQL query and executes it
//      $MySQLHandler->rawQuery($query)


/*=======================================tableSturcture Format=======================================*\

                        //--The table names are listed here
$tableSturcture['tables'] = array('main','users');
                        
                        //--The table column names to be used as primary keys are put here
$tableSturcture['keys'] = array('id','id');
                                
                                //--The table column names to be used as Unique keys are put here
$tableSturcture['unique'] =         array(
                                        array(
                                            'userId'
                                        ),

                                        array(
                                            ''
                                        )
                                    );

                        //--The table columns are listed here with the data types and options
$tableSturcture['columns'] =   array(
                                    array(
                                        'id INT NOT NULL AUTO_INCREMENT',
                                        'title TEXT',
                                        'text TEXT',
                                        'date TEXT',
                                        'tags TEXT',
                                        'status INT',
                                        'userid INT',
                                        'displaydate TEXT',
                                        'allowcomments INT'
                                    ),
                                    array(
                                        'id INT NOT NULL AUTO_INCREMENT',
                                        'name TEXT',
                                        'pass TEXT',
                                        'postcount INT',
                                        'type INT',
                                        'blogtitle TEXT',
                                        'blogurl TEXT',
                                        'blogpoststoshow INT',
                                        'blogshowtitle INT',
                                        'blogshownav INT',
                                        'blognavusestyle INT',
                                        'blognavtype TEXT',
                                        'blogfull TEXT',
                                        'blogheader TEXT',
                                        'blognav TEXT',
                                        'blogpost TEXT',
                                        'blogpostheader TEXT',
                                        'pagebuttons TEXT',
                                        'buttonsinblog INT'
                                    )
                                );
                
                


\*===================================================================================================*/


class MySQLHandler{

    //--Declare Feilds
    public $mysqli;
    public $sqlData;
    public $tableSturcture;

    /*
     * Constructor 
     * 
     * Checks if database matches defined sturcture and creates it if not
     */
    public function __construct($host,$user,$pass,$database){
        
        //--Error output Variable
        $error = 'nope';
        
        //--Convert columns into just names
        for($c = 0;$c <= count($basicTableSturcture['tables'])-1;$c++){
        
            for($d = 0;$d <= count($basicTableSturcture['columns'][$c])-1;$d++){
                
                $rawcolumnInfo = explode(' ',$basicTableSturcture['columns'][$c][$d],2);
                
                $basicTableSturcture['columnNames'][$c][$d] = $rawcolumnInfo[0];
            }
        }
        
        
                        //--The table names are listed here
        $this->tableSturcture['tables'] = array('itemInfo', 'imageInfo');
                                
                                //--The table column names to be used as primary keys are put here
        $this->tableSturcture['keys'] = array('imageId');
                                        
                                        //--The table column names to be used as Unique keys are put here
        $this->tableSturcture['unique'] =   array(
                                                array(
                                                    'imageId'
                                                )
                                            );

                                //--The table columns are listed here with the data types and options
        $this->tableSturcture['columns'] =   array(
            
                                            array(
                                                'itemInfo INT NOT NULL AUTO_INCREMENT',
                                                'title VARCHAR(256)',
                                                'description TEXT',
                                                'size FLOAT',
                                                'dateAdded TIMESTAMP',
                                                'type VARCHAR(64)',
                                                'typeId INT',
                                                'views INT'
                                            ),

                                            array(
                                                'imageId INT NOT NULL AUTO_INCREMENT',
                                                'width INT',
                                                'hieght INT',
                                                'keywords TEXT'
                                            ),

                                            array(
                                                'videoId INT NOT NULL AUTO_INCREMENT',
                                                'width INT',
                                                'hieght INT',
                                                'length INT',
                                                'bitrate INT'
                                            )
                                        );
        
        
        //--Begin sql connection
        $this->mysqli = new mysqli($host, $user, $pass);
        
        //--Error check and Output
        if ($this->mysqli->connect_errno) {
            echo "Error - MSH02CON001: Failed to connect to MySQL(" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
        }
        
        
        //Attept to connect to database  
        if(0 == $this->mysqli->select_db($database)){
        
            $query = 'CREATE DATABASE '.$database;
            
            //--Error check and Output
            If($this->mysqli->query($query) == 1){
            
                echo 'Created Database: '.$database."<br />\n";
                $this->mysqli->query('USE '.$database.';');
                    
            }else{
            
                echo 'Error - MSH01D002: Failed to add Database '.$database."<br />\n";
            }
            
        }
        //--Check if tables are set
        for($c = 0;$c <= count($this->tableSturcture['tables'])-1;$c++){
        
            //--Check if tables exist    
            if(false == $this->mysqli->query('SELECT 1 FROM '.$this->tableSturcture['tables'][$c].' LIMIT 1')){
                echo 'Checking Table: '.$this->tableSturcture['tables'][$c]."<br />\n";
                
                //--Define table
                $query = 'CREATE TABLE '.$this->tableSturcture['tables'][$c].'(';
                
                //--Define columns
                for($d = 0;$d <= count($this->tableSturcture['columns'][$c])-1;$d++){
                    
                    $query .= $this->tableSturcture['columns'][$c][$d].', ';
                }
                
                //--Define Primary Key and Finish Query
                $query .= 'PRIMARY KEY('.$this->tableSturcture['keys'][$c].')';
                $query .= ' )';
                
                //--Error check and Output
                If($this->mysqli->query($query) == 1){
                
                    echo 'Created Table: '.$this->tableSturcture['tables'][$c]."<br />\n";
                        
                }else{
                
                    echo 'Error - MSH01T003: Failed to add Table '.$this->tableSturcture['tables'][$c]."<br />\n";
                }   
            
            }

            //--Check if all columns exist 
            unset($result);
            $result = $this->mysqli->query('SELECT * FROM '.$this->tableSturcture['tables'][$c]);
            
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                
                for($d = 0;$d <= count($this->tableSturcture['columnNames'][$c])-1;$d++){
                
                    if( !(isset($row[ $this->tableSturcture['columnNames'][$c][$d] ])) ){
                        $query = 'ALTER TABLE '.$this->tableSturcture['tables'][$c].' ADD '.$this->tableSturcture['columns'][$c][$d].'';
                        
                        If($this->mysqli->query($query) == 1){
                        
                            echo 'Created Column '.$this->tableSturcture['columnNames'][$c][$d].'('.$this->tableSturcture['columns'][$c][$d].")<br />\n";
                            
                        }else{
                            echo 'Error - MSH01C004: Failed to add column '.$this->tableSturcture['columnNames'][$c][$d].'('.$this->tableSturcture['columns'][$c][$d].': '.$query.')';
                        }
                    }
                }
                
                
            }
            
            //--Check if all unique columns are set
            unset($result);
            for($d = 0;$d < count($this->tableSturcture['columnNames'][$c]);$d++){
            
                if( in_array($this->tableSturcture['columnNames'][$c][$d], $this->tableSturcture['unique'][$c]) ){

                    $query = 'SHOW INDEX FROM '.$this->tableSturcture['tables'][$c].' WHERE Column_name="'.$this->tableSturcture['columnNames'][$c][$d].'"';
                    $result = $this->mysqli->query($query);

                    If($result->fetch_assoc() == NULL){
                        $query = 'ALTER TABLE `'.$this->tableSturcture['tables'][$c].'` ADD UNIQUE(`'.$this->tableSturcture['columnNames'][$c][$d].'`)';
                        
                        if($this->mysqli->query($query) == 1){
                            echo 'Set Column '.$this->tableSturcture['columnNames'][$c][$d]." to be unique<br />\n";
                        }else{
                            echo 'Error - MSH01C005: Failed to set column '.$this->tableSturcture['columnNames'][$c][$d].' unique ( '.$query.' )';
                        }
                    }
                }
            }
            
            
            
        }
    }
    

    /*
     * getTable 
     * 
     * loads data from the specified table
     * 
     * Returns array of table
     * 
     * Flags
     * USE_KEY: Uses the primary key as the index in the output array
     * 
     */
    function getTable($table,$flags = 'none') {
        
        $output = NULL;
        //--Check for primary key override
        if($flags == 'USE_KEY'){
            $useKey = true;
        }else{
            $useKey = false;
        }
    
        //-find table key
        for($c = 0;$c <= count($this->tableSturcture['tables'])-1;$c++){
           
            if($this->tableSturcture['tables'][$c] == $table){
                
                $key = $c;
            }
        }
    
        //-find table primary Key column
        for($c = 0;$c <= count($this->tableSturcture['columnNames'])-1;$c++){
           
            if($this->tableSturcture['columnNames'][$key][$c] == $this->tableSturcture['keys'][$key]){
                
                $primaryKeycolumn = $this->tableSturcture['columnNames'][$key][$c];
            }
        }
        
        
        
        //--Read entries from table
        $result = $this->mysqli->query('SELECT * FROM '.$this->tableSturcture['tables'][$key]);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                
                unset($entry);
                for($c = 0;$c <= count($this->tableSturcture['columnNames'][$key])-1;$c++){
                    
                    $entry[ $this->tableSturcture['columnNames'][$key][$c] ] = $row[ $this->tableSturcture['columnNames'][$key][$c] ];
                }
                
                if($useKey){
                    $output[ $entry[$primaryKeycolumn] ] = $entry;
                }else{
                    $output[] = $entry;
                }
                
            }
        }
        
        
        //--Return table as array
        return $output;
    }
    

    /*
     * getEntry 
     * 
     * loads entry from the specified table
     * 
     * Returns array of the entry
     * 
     */
    function getEntry($table,$entryKey,$searchColumn = '',$flags = '') {
        
    
        $output = NULL;
        //--Check for a different column
        if($flags == 'DIFFERENT_COLMN'){
            $useKeyColumn = false;
        }else{
            $useKeyColumn = true;
        }
        
        
        //-find table key
        for($c = 0;$c <= count($this->tableSturcture['tables'])-1;$c++){
           
            if($this->tableSturcture['tables'][$c] == $table){
                
                $key = $c;
            }
        }
        
        //-find table primary Key column is it is too be used
        if($useKeyColumn){
            for($c = 0;$c <= count($this->tableSturcture['columnNames'])-1;$c++){
               
                if($this->tableSturcture['columnNames'][$key][$c] == $this->tableSturcture['keys'][$key]){
                    
                    $searchColumn = $this->tableSturcture['columnNames'][$key][$c];
                }
            }
        }
        
        //--Read entries from table
        $result = $this->mysqli->query('SELECT * FROM '.$this->tableSturcture['tables'][$key].' WHERE '.$searchColumn.'="'.$entryKey.'"');
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                
                
                for($c = 0;$c <= count($this->tableSturcture['columnNames'][$key])-1;$c++){
                    
                    $entry[ $this->tableSturcture['columnNames'][$key][$c] ] = $row[ $this->tableSturcture['columnNames'][$key][$c] ];
                }
                
                $output = $entry;
                
            }
        }
        
        
        //--Return table as array
        return $output;
    }
    
    
    /*
     * addEntry 
     * 
     * Adds a new entry to a table
     * 
     * Returns mysqli->query result
     * 
     * Flags
     * OVERRIDE_KEY: Overides the primary key check letting you specify one
     * 
     */
    function addEntry($table,$data,$flags = 'none') {
        
        //--Check for primary key override
        if($flags == 'OVERRIDE_KEY'){
            $dontSkipKey = true;
        }else{
            $dontSkipKey = false;
        }
        
        //-find table key
        for($c = 0;$c <= count($this->tableSturcture['tables'])-1;$c++){
           
            if($this->tableSturcture['tables'][$c] == $table){
                
                $key = $c;
            }
        }
        
        //--Start query
        $query = 'INSERT INTO '.$this->tableSturcture['tables'][$key].' (';
        
        //--Get columns
        for($c = 0;$c <= count($this->tableSturcture['columnNames'][$key])-1;$c++){
        
            //--Check for Primary Key
            if($this->tableSturcture['columnNames'][$key][$c] != $this->tableSturcture['keys'][$key] || $dontSkipKey){
            
                $nextComma = '';
                if( !($c+1 > count($this->tableSturcture['columnNames'][$key])-1) ){
                    $nextComma = ', ';
                }
                $query .= $this->tableSturcture['columnNames'][$key][$c].$nextComma;
            }
        }
        
        //--End columns and start Values
        $query .= ') VALUES (';
        
        //--Get columns
        for($c = 0;$c <= count($data)-1;$c++){
            
            $nextComma = '';
            if( !($c+1 > count($data)-1) ){
                $nextComma = ', ';
            }
            $query .= ' "'.$data[$c].'"'.$nextComma;
        }
        
        //--End query
        $query .= ')';
        
        //--Executes query and returns result
        return $this->mysqli->query($query);
        
        
    }
    

    /*
     * uptadeEntry 
     * 
     * Modifies an entry from a table
     * 
     * Returns mysqli->query result
     * 
     * Flags
     * OVERWRITE_KEY: Overides the primary key check letting you overwrite it
     * 
     */
    function uptadeEntry($table,$entryKey,$data,$flags = 'none') {
        
        //--Check for primary key override
        if($flags == 'OVERWRITE_KEY'){
            $dontSkipKey = true;
        }else{
            $dontSkipKey = false;
        }
        
        //-find table key
        for($c = 0;$c <= count($this->tableSturcture['tables'])-1;$c++){
           
            if($this->tableSturcture['tables'][$c] == $table){
                
                $key = $c;
            }
        }
        
        //--Start query
        $query = 'UPDATE '.$this->tableSturcture['tables'][$key].' SET ';
        
        //--Get columns
        for($c = 0;$c <= count($this->tableSturcture['columnNames'][$key])-1;$c++){
        
            //--Check for Primary Key
            if($this->tableSturcture['columnNames'][$key][$c] != $this->tableSturcture['keys'][$key] || $dontSkipKey){
            
                $filteredcolumns[] = $this->tableSturcture['columnNames'][$key][$c];
            }
        }
                
        //--Get columns
        for($c = 0;$c <= count($data)-1;$c++){
        
            $nextComma = '';
            if( !($c+1 > count($data)-1) ){
                $nextComma = ', ';
            }
            $query .= $filteredcolumns[$c].'="'.$data[$c].'"'.$nextComma;
        }
        
        //--End query
        echo $query .= ' WHERE '.$this->tableSturcture['keys'][$key].'="'.$entryKey.'"';
        
        //--Executes query and returns result
        return $this->mysqli->query($query);
        
        
    }
    

    /*
     * deleteEntry 
     * 
     * Deletes an entry from a table
     * 
     * Returns mysqli->query result
     * 
     */
    function deleteEntry($table,$entryKey) {
                
        //-find table key
        for($c = 0;$c <= count($this->tableSturcture['tables'])-1;$c++){
           
            if($this->tableSturcture['tables'][$c] == $table){
                
                $key = $c;
            }
        }
        
        //--Form query
        $query = 'DELETE FROM '.$this->tableSturcture['tables'][$key].' WHERE '.$this->tableSturcture['keys'][$key].'="'.$entryKey.'"';
        
        
        //--Executes query and returns result
        return $this->mysqli->query($query);
        
        
    }
    

    /*
     * rawQuery 
     * 
     * Accepts a raw SQL query and executes it
     */
    function rawQuery($query) {
        
        //--Executes query and returns result
        return $this->mysqli->query($query);
        
        
    }
    

    /*
     * rawQuery 
     * 
     * Accepts a prepared SQL query and data to be added to it and then executes it
     * PARAM $data is an array with the bind_param data. The first item should have the types with the rest being the data
     */
    function preparedQuery($query,$data) {
        $stmt = $this->mysqli->stmt_init();
        $result = 'Broked';
        if($stmt->prepare($query)){
            call_user_func_array(array($stmt, "bind_param"), $data);

            $stmt->execute();
        
            //from php.net

            $meta = $stmt->result_metadata();
            if($meta == false){

                $result = 'Succes';
            }else{
                $result = array();
                while( $field = $meta->fetch_field() ){
                    $params[] = &$row[$field->name];
                }

                call_user_func_array(array($stmt, 'bind_result'), $params);

                while( $stmt->fetch() ){
                    foreach($row as $key => $val){
                        $c[$key] = $val;
                    }
                    $result[] = $c;
                }
            }
           
            $stmt->close(); 
            //from php.net



        }

        return $result;
    }
    

    /*
     * destructor 
     * 
     * Closes database conection
     */
    function __destruct() {
        
        //--Disconnect from database
        mysqli_close($this->mysqli);
    }
}
/*===============================================================Error Code Guide===============================================================*\
 *                                                                                                                                              *
 * Here is an example code                                                                                                                      *
 *                                                                                                                                              *
 *        MSH01E001                                                                                                                             *
 *                                                                                                                                              *
 * [MSH] The first three characters represent that it is  a MySQLHandler error.                                                                 * 
 *                                                                                                                                              *
 * [01] The next two numbers representthe action type. Here are the actions and their codes:                                                    *
 *                                                                                                                                              *
 * - 00: Undefined                                                                                                                              *
 * - 01: Write                                                                                                                                  *
 * - 02: Read                                                                                                                                   *
 * - 03: Modify                                                                                                                                 *
 * - 04: Delete                                                                                                                                 *
 *                                                                                                                                              *
 * [P] The next character identifies whether is was a connection(CON), database(D), table(T), column(C), or entry(E) error.                     *
 *                                                                                                                                              *
 * [001] The next three numbers identify a particular peice  of code. They will show where the error happened. Here are the identities:         *
 *                                                                                                                                              *
 * 001: Failed to connect to MySQL                                                                                                              *
 * 002: Failed to add Database                                                                                                                  *
 * 003: Failed to add Table                                                                                                                     *
 * 004: Failed to add column                                                                                                                    *
 * 005: Failed to set column unique                                                                                                             *
 *                                                                                                                                              *
 *                                                                                                                                              *
\*===============================================================Error Code Guide===============================================================*/
?>
