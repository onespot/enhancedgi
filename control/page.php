<?php

$_REPO = 'amplify-back-end';
$_ACCOUNT = 'onespot';

require_once '../lib/misc.php';
require_once '../lib/github-api/lib/Github/Autoloader.php';
require_once '../model/issue.php';
require_once('../lib/db.php');
require_once '../lib/php-github-api/vendor/autoload.php';

class PageController{
	
	public $db;
	public $github;
	public $githubv3;
	public $repos;
	
    function PageController($username="", $password=""){
		global $_REPO,$_ACCOUNT;
		Github_Autoloader::register();
		$this->github = new Github_Client();
		$this->githubv3 = new Github\Client();
        $this->authenticate($username,$password);
		//$this->repos = $this->github->getRepoApi()->getOrgRepos($_ACCOUNT);
		$this->db=new Database();
    }
	
	function authenticate($user="", $pass=""){
		if (!isset($_SERVER['PHP_AUTH_USER']) && empty($user)) {
			header('WWW-Authenticate: Basic realm="Onespot Enhanced GI"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Please login using you Gitub username and password';
			exit;
		} else {
			$username=empty($user)?$_SERVER['PHP_AUTH_USER']:$user;
			$password = empty($pass)?$_SERVER['PHP_AUTH_PW']:$pass;
			
			$this->github->authenticate($username, $password, Github_Client::AUTH_HTTP_PASSWORD);
			$this->githubv3->authenticate($username, $password, Github\Client::AUTH_HTTP_PASSWORD);
			if(isset($_COOKIE["os-enhancedgi_auth"]) && $_COOKIE["os-enhancedgi_auth"] != md5($username.$password)){
				// hit the api to validate
				try{
				$res=$this->github->getUserApi()->show($username);
				setcookie("os-enhancedgi_auth",md5($username.$password),time()+3600000);
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
}