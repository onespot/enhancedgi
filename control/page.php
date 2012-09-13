<?php

$_REPO = 'amplify-back-end';
$_ACCOUNT = 'onespot';

require_once '../lib/misc.php';
require_once '../lib/github-api/lib/Github/Autoloader.php';
require_once '../model/issue.php';
require_once('../lib/db.php');
require_once '../lib/github-api-v3/vendor/autoload.php';

class PageController{
	
	public $db;
	public $github;
	public $githubv3;
	public $repos;
	
    function PageController(){
		global $_REPO,$_ACCOUNT;
		
		Github_Autoloader::register();
		$this->github = new Github_Client();
		$this->githubv3 = new Github\Client();
        $this->authenticate();
		$this->repos = $this->github->getRepoApi()->getOrgRepos($_ACCOUNT);
		$this->db=new Database();
    }
	
	function authenticate(){
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="Onespot Enhanced GI"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Please login using you Gitub username and password';
			exit;
		} else {
			//echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
			//echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
			$this->github->authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], Github_Client::AUTH_HTTP_PASSWORD);
			$this->githubv3->authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], Github\Client::AUTH_HTTP_PASSWORD);
			// hit the api to validate
			try{
			$res=$this->github->getUserApi()->show($_SERVER['PHP_AUTH_USER']);
			}catch(Exception $e){
				if($e->getMessage()==="HTTP 401: Unauthorized"){
					header('WWW-Authenticate: Basic realm="Onespot Enhanced GI"');
					header('HTTP/1.0 401 Unauthorized');
					echo 'Please login using you Gitub username and password';
					exit;
				}else{
					header('HTTP/1.0 500 Internal Server Error');
					echo "oops something is very wrong<br />".$e->getMessage();
					exit;
				}
			}
			
		}
	}
}