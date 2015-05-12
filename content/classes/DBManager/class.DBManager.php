<?php
class DBManager
{
	private $postData;
	private $dblogin;
	private $mysqli;
	private $pdo;
	private $patches;
	private $dbNewPatches;
	private $patchDir;
	private $showback = false;
	public $debug;

	private $showPublicAlert = false;

	/*
	 * constructor
	 * 
	 * 
	 */
	public function __construct($db_host,$db_user,$db_pass,$patchDirPath = "default")
	{
		$this->dblogin[0] = $db_host;
		$this->dblogin[1] = $db_user;
		$this->dblogin[2] = $db_pass;

		$this->postData = $_POST;
		$this->debug = "";
		if ( $patchDirPath == "default" )
		{
			$this->patchDir = dirname(__FILE__)."/patches/";
		}else{
			$this->patchDir = $patchDirPath;
			if( substr($this->patchDir, strlen($this->patchDir) - 1) != "/")
			{
				$this->patchDir .= "/";
			}
		}
		//--Begin sql connection
		$this->mysqli = new mysqli(
			$db_host,
			$db_user,
			$db_pass
		);

		$this->loadPatches();
		$this->checkDBs();
		$this->dbNewPatches = $this->getNewPatches();


		echo $this->debug;
	}

	/*
	 * Loads sql patches
	 */
	private function loadPatches()
	{
        $galdir = new DirectoryIterator($this->patchDir);
        foreach( $galdir as $entry )
        {    
            if ( ! $entry->isDot() ) 
            {
                if ( $entry->isFile() && strtoupper(pathinfo($entry->getFilename(), PATHINFO_EXTENSION)) == 'SQL' )
                {
                	$fileinfo = explode(',', $entry->getFilename());
                	$this->patches[$fileinfo[0]][] = substr( $fileinfo[1],0, strlen($fileinfo[1])-4 );
                }
            }
        }
	}


	private function checkDBs()
	{
		foreach ($this->patches as $database => $patchNames) 
		{
			$result = $this->mysqli->query("CREATE DATABASE IF NOT EXISTS ".$database);
			$this->mysqli->select_db($database);
			$result = $this->mysqli->query("SHOW TABLES LIKE 'dbm_history'");
			if ( $result->num_rows == 0)
			{
				$this->addTable();
			}

			foreach ($patchNames as $patch) 
			{
				$patchInfo = $this->getPatchInfo($database, $patch);
				$result = $this->mysqli->query("SELECT dbm_patch_exists('".$patch."')");
				
				if ( $result->fetch_row()[0] == "0")
				{
					$this->mysqli->query("CALL dbm_patch_addnew('".$patch."' ,'".$database."' ,'".$patchInfo[0]."' ,'".$patchInfo[1]."' ,'".$patchInfo[2]."')");
				}

			}

		}
	}

	private function getNewPatchInput()
	{
		$recivedData = false;
		if(sizeof($this->dbNewPatches) != 0)
		{
			foreach ($this->dbNewPatches as $patch) 
			{

				if ( isset($this->postData['install'.$patch[0]]) )
				{
					$recivedData = true;
					$this->showback = true;
				}

				if($recivedData && $patch[5] == 1 && !isset($this->postData['install'.$patch[0]]))
				{
					echo 'You must install required patches';
					$recivedData = false;
					$this->showback = true;
				}
			}
		}
		return $recivedData;
	}

	private function installPatches()
	{
		if(sizeof($this->dbNewPatches) != 0)
		{
			foreach ($this->dbNewPatches as $patch) 
			{

				if ( isset($this->postData['install'.$patch[0]]) && ( $patch[5] == 1 || intval($this->postData['install'.$patch[0]]) == 1) )
				{
					try
					{
						$pdo = new PDO('mysql:dbname='.$patch[1].';host='.$this->dblogin[0],$this->dblogin[1],$this->dblogin[2]);
						$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

						$patchSQL = file_get_contents($this->patchDir.$patch[1].",".$patch[0].".sql");
						$patchSQL = str_replace("[%]init_password[/%]", $this->dblogin[2], $patchSQL);
						$pdo->beginTransaction();
						$qr = $pdo->exec($patchSQL);
						$pdo->commit();
						if ( !($qr === false) )
						{	
							$pdo->exec("CALL dbm_patch_set_installed('".$patch[0]."')");
							echo "<p>Successfully installed patch ".$patch[0].'</p>';
						}else{
							echo "Error running patch: ".$patch[0];
							echo "<br />";
							echo '<pre>'.var_dump($qr).'</pre>';
							echo "<br />";
							echo '<pre>'.$patchSQL.'</pre><br />';
						}
					}
					catch (PDOException $e)
					{
						echo 'Connection failed: ' . $e->getMessage();
					}

				}elseif ( isset($this->postData['install'.$patch[0]]) && ( intval($this->postData['install'.$patch[0]]) == 2) )
				{
					$pdo = new PDO('mysql:dbname='.$patch[1].';host='.$this->dblogin[0],$this->dblogin[1],$this->dblogin[2]);
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
					$pdo->exec("CALL dbm_patch_set_ignored('".$patch[0]."')");	
					echo "<p>Successfully ignored patch ".$patch[0].'</p>';			
				}
			}
		}
	}

	private function getNewPatches()
	{

    	$dbPatches = array();
		$result = $this->mysqli->query("CALL dbm_patch_get_needsAction");
		if ( $result->num_rows != 0)
		{
			while ($row = $result->fetch_row()) {
	        	$dbPatches[] = $row;
	    	}
	    }

    	return $dbPatches;
	}

	public function displayPublicAlert($linkText="",$linkURL="")
	{
		$numPatches = sizeof($this->dbNewPatches);
		if($numPatches > 0)
		{
			$multi = 'are '.$numPatches.' patches that need';
			if($numPatches == 1)
			{
				$multi = 'is a patch that needs';
			}
			echo '

<link rel=StyleSheet href="content/classes/DBManager/dbmanager.css" type="text/css">
<div id="dbm_hideaway">
	<p>DBManager: There '.$multi.' action</p>
	<a href="'.$linkURL.'">'.$linkText.'</a>
</div>
		';
		}
	}

	public function getPatchForm()
	{
		echo '
<link rel=StyleSheet href="content/classes/DBManager/dbmanager.css" type="text/css">
<form id="dbm_patchtable" method="post">
		<header>
			<h3> DB Manager</h3>
		</header>
';
		if( ! $this->getNewPatchInput() )
		{
			
			echo '
	<table>
		<tr>
			<th colspan="7" class="tbHeader"> New Patches </th>
		</tr>
		<tr>
			<th> Install/Ignore? </th>
			<th> Id </th>
			<th> Database </th>
			<th> Description </th>
			<th> Data Intergrity </th>
			<th> Date Added </th>
			<th> Required </th>
		</tr>
		';
		
		if(sizeof($this->dbNewPatches) == 0)
		{
			echo '
		<tr>
			<td colspan=7 style="text-align: center;">  No new patches found</td>
		</tr>';
		}else{
			foreach ($this->dbNewPatches as $patch) 
			{
				$checked = '';
				if($patch[5] == 1)
				{
					$install = '<input type="checkbox" id="install'.$patch[0].'" name="install'.$patch[0].'" checked  onclick="return false" />';
				}else
				{
					$install = '<input type="radio" id="install'.$patch[0].'" name="install'.$patch[0].'" value=1 />/<input type="radio" id="install'.$patch[0].'" name="install'.$patch[0].'" value=2 />';
				}
				echo '
		<tr>
			<td> '.$install.' </td>
			<td> '.$patch[0].' </td>
			<td> '.$patch[1].' </td>
			<td> '.$patch[2].' </td>
			<td> '.$patch[3].' </td>
			<td> '.$patch[6].' </td>
			<td> '.$this->getRequiredText($patch[5]).' </td>
		</tr>
		';
			}

		echo '

		<tr>
			<td colspan="7" class="tbSubmit" > <input type="submit" value="Install Patches" /> </td>
		</tr>';
		}
		echo '

	</table>';

		}else{
			$this->installPatches();
		}
		if ($this->showback)
		{
			echo '<br /><a href="?" class="linkButton">Back</a>';
		}
	echo '
</form>		
		';
	}

	private function addTable()
	{
		$this->mysqli->query('

CREATE TABLE dbm_history
(
	patch VARCHAR(9) NOT NULL,
	pdatabase VARCHAR(64) NOT NULL,
	description VARCHAR(250) ,
	data_intergrity VARCHAR(250) ,
	status INT NOT NULL DEFAULT 0,
	required INT NOT NULL,
	date DATETIME NOT NULL,
	UNIQUE KEY (patch)
);');

		$this->mysqli->query('DROP FUNCTION IF EXISTS dbm_patch_exists');
		
		$this->mysqli->query('
CREATE FUNCTION dbm_patch_exists (inPatch VARCHAR(9)) RETURNS BOOL
BEGIN
    DECLARE isExist BOOL;
    SET isExist = 0;
    SELECT EXISTS(SELECT * FROM dbm_history WHERE `patch`=inPatch) INTO isExist ;
    RETURN isExist;
END 
');

		$this->mysqli->query('DROP PROCEDURE IF EXISTS dbm_patch_addnew ');

		$this->mysqli->query('
CREATE PROCEDURE dbm_patch_addnew (inPatch VARCHAR(9) , inDatabase VARCHAR(64) , inDescription VARCHAR(250) , inDataIntergrity VARCHAR(250) , inRequired INT)
BEGIN
	INSERT INTO dbm_history (patch,pdatabase,description,data_intergrity,required,date) VALUES (inPatch ,inDatabase ,inDescription ,inDataIntergrity , inRequired , NOW());
END 

');

		$this->mysqli->query('DROP PROCEDURE IF EXISTS dbm_patch_get_needsAction ');

		$this->mysqli->query('
CREATE PROCEDURE dbm_patch_get_needsAction ()
BEGIN
SELECT * FROM dbm_history 
WHERE `status` = "0"
ORDER BY `patch` ASC;
END


');

		$this->mysqli->query('DROP PROCEDURE IF EXISTS dbm_patch_set_installed ');

		$this->mysqli->query('
CREATE PROCEDURE dbm_patch_set_installed ( inPatch VARCHAR(9) )
BEGIN
UPDATE dbm_history SET status = 1 WHERE dbm_history.patch=inPatch;
END


');

		$this->mysqli->query('DROP PROCEDURE IF EXISTS dbm_patch_set_ignored ');

		$this->mysqli->query('
CREATE PROCEDURE dbm_patch_set_ignored ( inPatch VARCHAR(9) )
BEGIN
UPDATE dbm_history SET status = 2 WHERE dbm_history.patch=inPatch;
END


');

		echo $this->mysqli->error;

	}

	private function getPatchInfo($database, $patch)
	{
		$description = "";
		$descriptionFound = false;
		$dataIntergrity = "";
		$dataIntergrityFound = false;
		$required = "2";
		$requiredFound = false;

		$file_handle = fopen($this->patchDir.$database.",".$patch.".sql", "r");
		while (!feof($file_handle)) {
			$line = fgets($file_handle);
			if (strpos(strtoupper($line),'DESCRIPTION') !== false && $descriptionFound == false) {
				$description .= substr($line, 12);
				$description = str_replace("\n","",$description);
				$descriptionFound = true;
			}
			if (strpos(strtoupper($line),'DATA INTEGRITY') !== false && $dataIntergrityFound == false) {
				$dataIntergrity .= substr($line, 15);
				$dataIntergrity = str_replace("\n","",$dataIntergrity);
				$dataIntergrityFound = true;
			}
			if (strpos(strtoupper($line),'REQUIRED') !== false && $requiredFound == false) {
				$required = trim(substr($line, 9));
				$required = str_replace("\n","",$required);
				$requiredFound = true;

				if (strtoupper($required) == "YES")
				{
					$required = 1;
				}
				elseif (strtoupper($required) == "NO")
				{
					$required = 0;
				}
				else
				{
					$required = 2;
				}
			}
			
		}
			
		fclose($file_handle);

		return array($description,$dataIntergrity,$required);
	}

	private function getRequiredText($required)
	{
		switch ($required) {
			case 0:
				return "No";			
			case 1:
				return "Yes";
			default:
				return "Unknown";
		}
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
?>
