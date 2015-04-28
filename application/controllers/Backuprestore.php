<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backuprestore extends CI_Controller
{
	const MAX_FILE_SIZE = 262144000;
	
	const UPLOAD_FILE_DIR = "./uploads";
	
	const BACKUP_FILE_DIR = "./backups";
	
	const HMAC_KEY_FILE = "hmac.key";
	
	const ENCRYPT_KEY_FILE = "crypt.key";
	
	public function index()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
			redirect('Login/logout');
		
		$data = array('user' => $user, "files" => self::getBackupFiles());
		
		$this->load->view('backuprestore_index_view', $data);
	}
	
	public function backup()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
		{
			redirect('Login/logout');
		}
		
		$new_file_path = self::BACKUP_FILE_DIR . "/" . hash('md5', time() . rand()) . ".backup";
		
		$servername = "127.0.0.1";
		$username = "root";
		$password = ""; 
		$dbname = "csc_webapp";

		// Create connection
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		
		if(!$conn)
		{
			header("Content-type: text/plain", true, 500);
			echo "Failed to connect to database";
			return;
		}
		
		$file = fopen($new_file_path, "w");
		
		$content = "";
		
		$content .= "date:" . date("F j, Y - g:i:s A") . "\n";
		
		$query = "SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname'";
		
		$table_names = array();
		
		$results = mysqli_query($conn, $query);
		
		if(mysqli_num_rows($results) > 0)
		{
			while($row = mysqli_fetch_assoc($results))
			{
				array_push($table_names, $row['table_name']);
			}
		}
		
		foreach($table_names as $table_name)
		{
			$content .= "table:$table_name\n";
			
			$query = "SELECT column_name, data_type FROM information_schema.columns WHERE table_schema = '$dbname' AND table_name = '$table_name'";
			
			$results = mysqli_query($conn, $query);
			
			$column_types = array();
			
			if(mysqli_num_rows($results) > 0)
			{
				$first = true;
				
				while($row = mysqli_fetch_assoc($results))
				{
					if($first)
					{
						$first = false;
					}
					else
					{
						$content .= ",";
					}
					
					array_push($column_types, $row['data_type']);
					
					$column_name = $row['column_name'];
					
					$content .= "`$column_name`";
				}
			}
			
			$content .= "\n";
			
			$query = "SELECT * FROM `$table_name`";
			
			$results = mysqli_query($conn, $query);
			
			if(mysqli_num_rows($results) > 0)
			{
				$outer_first = true;
				
				while($row = mysqli_fetch_array($results))
				{
					if($outer_first)
					{
						$outer_first = false;
					}
					else
					{
						$content .= "\n";
					}
					
					$index = 0;
					
					$inner_first = true;
					
					foreach($column_types as $column_type)
					{
						if($inner_first)
						{
							$inner_first = false;
						}
						else
						{
							$content .= ",";
						}
						
						$value = $row[$index];
						
						if($column_type == "varchar" || $column_type == "char")
						{
							$content .="'$value'";
						}
						else
						{
							$content .= "$value";
						}
						
						$index++;
					}
				}
				
				$content .= "\n";
			}
		}
		
		$hash_content = self::getHMACKey() . $content;
		
		$hmac = hash('sha256', $hash_content);
		
		$content = "hmac:" . $hmac . "\n" . $content;
		
		$crypt = new Encryption(self::getEncryptionKey());
		
		$content = $crypt->encrypt($content);
		
		fwrite($file, $content);
		
		fclose($file);
		
		redirect("Backuprestore/index");
	}
	
	public function delete()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
		{
			redirect('Login/logout');
		}
		
		if($this->uri->segment(3) && file_exists(self::BACKUP_FILE_DIR . "/" . $this->uri->segment(3)))
		{
			$file_url = self::BACKUP_FILE_DIR . "/" . $this->uri->segment(3); 
			
			unlink($file_url);
		}
		
		redirect('Backuprestore/index');
	}
	
	public function download()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
		{
			redirect('Login/logout');
		}
		
		if($this->uri->segment(3) && file_exists(self::BACKUP_FILE_DIR . "/" . $this->uri->segment(3)))
		{
			$file_url = self::BACKUP_FILE_DIR . "/" . $this->uri->segment(3); 
			
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
			readfile($file_url);
		}
		else
		{
			redirect('Backuprestore/index');
		}
	}
	
	public function restore()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
		{
			header("Content-type: text/plain", true, 401);
			echo "Unauthorized access";
			return;
		}
		
		if(!$this->uri->segment(3) || !file_exists(self::BACKUP_FILE_DIR . "/" . $this->uri->segment(3)))
		{
			header("Content-type: text/plain", true, 404);
			echo "File not found";
			return;
		}
		
		$file_path = self::BACKUP_FILE_DIR . "/" . $this->uri->segment(3);
		
		$contents = self::validateFile($file_path, true);
		
		if($contents === false)
		{
			header("Content-type: text/plain", true, 500);
			echo "Invalid file to restore from!";
			return;
		}
		
		$servername = "127.0.0.1";
		$username = "root";
		$password = ""; 
		$dbname = "csc_webapp";

		// Create connection
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		
		if(!$conn)
		{
			header("Content-type: text/plain", true, 500);
			echo "Failed to connect to database";
			return;
		}
		
		set_time_limit(0);
		ignore_user_abort(1);
		
		mysqli_query($conn, "SET foreign_key_checks = 0");
		
		$query = "SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname'";
		
		$results = mysqli_query($conn, $query);
		
		$table_names = array();
		
		if(mysqli_num_rows($results) > 0)
		{
			while($row = mysqli_fetch_assoc($results))
			{
				$index = $row['table_name'];
				
				$table_names[$index] = array();
			}
		}
		
		$current_table = "";
		
		$query_start = "";
		
		$table_name_prev_line = false;
		
		header("Content-type: text/plain", true, 200);
		
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $contents) as $line)
		{
			// get the table name
			if(strpos($line, "table:") === 0)
			{
				$table_name = substr($line, 6);
				
				$query_start = "";
				
				if(array_key_exists($table_name, $table_names))
				{
					$current_table = $table_name;
					
					mysqli_query($conn, "TRUNCATE TABLE `$current_table`;");
					
					mysqli_query($conn, "OPTIMIZE TABLE `$current_table`;");
					
					$table_name_prev_line = true;
				}
				else
				{
					$current_table = "";
				}
			}
			// gather column information about table
			else if($table_name_prev_line)
			{
				$query_start .= "INSERT INTO `$table_name` ($line) VALUES";
				
				$table_name_prev_line = false;
			}
			// reading rows for the table
			else
			{
				if(strlen($current_table) > 0 && strlen($query_start) > 0)
				{
					mysqli_query($conn, "$query_start ($line);");
				}
			}
		} 
		
		mysqli_query($conn, "SET foreign_key_checks = 1");
		
		redirect('Backuprestore/index');
	}
	
	public function submit()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
		{
			header("Content-type: text/plain", true, 401);
			echo "Unauthorized access";
			return;
		}
		
		// Check $_FILES['upfile']['error'] value.
		switch ($_FILES['boss_file']['error']) 
		{
			case UPLOAD_ERR_OK:
				break;
				
			case UPLOAD_ERR_NO_FILE:
				header("Content-type: text/plain", true, 400);
				echo "No file sent";
				return;
				
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				header("Content-type: text/plain", true, 400);
				echo "Exceeded file size limit";
				return;
				
			default:
				header("Content-type: text/plain", true, 500);
				echo "Unknown error occurred";
				return;
		}
		
		// You should also check filesize here. 
		if ($_FILES['boss_file']['size'] > self::MAX_FILE_SIZE) 
		{
			header("Content-type: text/plain", true, 400);
			echo "Exceeded file size limit";
			return;
		}
		
		$file_name = hash("md5", time() . $_FILES['boss_file']['tmp_name']);
		
		$file_path = self::BACKUP_FILE_DIR . "/" . $file_name . ".backup";
		
		if (!move_uploaded_file($_FILES['boss_file']['tmp_name'], $file_path))
		{
			header("Content-type: text/plain", true, 500);
			echo "Failed to move uploaded file";
			return;
		}
		
		if($result == null)
		{
			header("Content-type: text/plain", true, 200);
			echo "Success";
		}
		else
		{
			header("Content-type: text/plain", true, 400);
			echo $result;
		}
	}
	
	private static function getBackupFiles()
	{
		$arr = array();
		
		foreach(scandir(self::BACKUP_FILE_DIR) as $file)
		{
			if($file === '.' || $file === '..' || $file === self::ENCRYPT_KEY_FILE || $file === self::HMAC_KEY_FILE || $file === '.htaccess')
			{
				continue;
			}
			
			$file_title = self::validateFile(self::BACKUP_FILE_DIR . "/" . $file);
			
			if($file_title)
			{
				array_push($arr, array("title" => $file_title, "file" => $file));
			}
		}
		
		return $arr;
	}
	
	private static function getHMACKey()
	{
		$file_path = self::BACKUP_FILE_DIR . "/" . self::HMAC_KEY_FILE;
		
		if(file_exists($file_path))
		{
			return file_get_contents($file_path);
		}
		else
		{
			$key = hash('sha256', time());
			
			file_put_contents($file_path, $key);
			
			return $key;
		}
	}
	
	private static function getEncryptionKey()
	{
		$file_path = self::BACKUP_FILE_DIR . "/" . self::ENCRYPT_KEY_FILE;
		
		if(file_exists($file_path))
		{
			return file_get_contents($file_path);
		}
		else
		{
			$key = hash('md5', time() . rand());
			
			file_put_contents($file_path, $key);
			
			return $key;
		}
	}
	
	private static function validateFile($file_path, $return_contents = false)
	{
		$file_contents = file_get_contents($file_path);
			
		$crypt = new Encryption(self::getEncryptionKey());
		
		try
		{
			$file_contents = $crypt->decrypt($file_contents);
		}
		catch(Exception $e)
		{
			return false;
		}
		
		$hmac_start_index = strpos($file_contents, "hmac:");
		
		if($hmac_start_index === false)
		{
			return false;
		}
		
		$hmac_start_index += 5;
		$hmac_end_index = strpos($file_contents, "\n", $hmac_start_index);
		
		if(!$hmac_end_index || $hmac_end_index < $hmac_start_index)
		{
			return false;
		}
		
		$hmac = substr($file_contents, $hmac_start_index, $hmac_end_index - $hmac_start_index);
		
		$file_contents = substr($file_contents, $hmac_end_index + 1);
		
		$file_hmac = hash('sha256', self::getHMACKey() . $file_contents);
		
		if($hmac !== $file_hmac)
		{
			return false;
		}
		
		$date_start_index = strpos($file_contents, "date:");
		
		if($date_start_index === false)
		{
			return false;
		}
		
		$date_start_index += 5;
		$date_end_index = strpos($file_contents, "\n", $date_start_index);
		
		if(!$date_end_index || $date_end_index < $date_start_index)
		{
			return false;
		}
		
		$date = substr($file_contents, $date_start_index, $date_end_index - $date_start_index);
		
		if($return_contents)
		{
			return substr($file_contents, $date_end_index + 1);
		}
		else
		{
			return $date;
		}
	}
}

class Encryption
{
	const CIPHER = MCRYPT_RIJNDAEL_128; // Rijndael-128 is AES
	const MODE   = MCRYPT_MODE_CBC;

	/* Cryptographic key of length 16, 24 or 32. NOT a password! */
	private $key;
	public function __construct($key) {
		$this->key = $key;
	}

	public function encrypt($plaintext) {
		$ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_RANDOM);
		$ciphertext = mcrypt_encrypt(self::CIPHER, $this->key, $plaintext, self::MODE, $iv);
		return base64_encode($iv.$ciphertext);
	}

	public function decrypt($ciphertext) {
		$ciphertext = base64_decode($ciphertext);
		$ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
		if (strlen($ciphertext) < $ivSize) {
			throw new Exception('Missing initialization vector');
		}

		$iv = substr($ciphertext, 0, $ivSize);
		$ciphertext = substr($ciphertext, $ivSize);
		$plaintext = mcrypt_decrypt(self::CIPHER, $this->key, $ciphertext, self::MODE, $iv);
		return rtrim($plaintext, "\0");
	}
}