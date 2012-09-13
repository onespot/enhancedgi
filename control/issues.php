<?phprequire_once '../control/page.php';class IssuesController extends PageController{	public $users;	public $issuesByUser;	public $milestones;	public $features;	public $issues;	public $issuesByFeature;	public $issuesByMilestone;	    function IssuesController(){		global $_REPO,$_ACCOUNT;		parent::PageController();				$repos = $_GET["repos"]; 		if(empty($repos)){			$repos=array();			foreach($this->repos as $repo){				$repos[]=$repo['name'];			}		}		$issues=array();		// check for a single repo specified		if(!empty($_GET['repo'])){			// limit to single repo			$issues = $this->getIssues($_GET['repo']);		}else{			// load issues accross all specified repos			$cacheFile="../cache/".hashSomething($repos);			if(file_exists($cacheFile) && time() - filemtime($cacheFile) < 3600){				$fh = fopen($cacheFile, 'r');				$issues = json_decode(fread($fh, filesize($cacheFile)));				fclose($fh);			}else{				foreach($repos as $repo){					$issues = array_merge($this->getIssues($repo),$issues);				}				// cache issues on disk				$fh = fopen($cacheFile, 'w');				fwrite($fh, json_encode($issues));				fclose($fh);			}		}		$this->processIssues($issues);    }		function getIssues($repo){		global $_REPO,$_ACCOUNT;			$issuesArrays = $this->github->getIssueApi()->getList($_ACCOUNT, $repo, 'open');		$issues=array();		foreach($issuesArrays as $issueArray){			$issues[]=arrayToObject($issueArray);		}		return $issues;	}		function processIssues($api_issues){		// Create the Issue Objects		$issue_objects=array();		foreach($api_issues as $issue){			// create Issue objects			$issue_object = new Issue($this->db,$issue);			$issue_objects[]=$issue_object;		}		//usort($issue_objects,'cmp_milestone_priority');		usort($issue_objects,'cmp_issue_priority');				$this->issues=$issue_objects;				$this->users=array();		// Organize the issues by user		$this->issuesByUser = array();		foreach($issue_objects as $issue){			if(!array_key_exists($issue->_issue->assignee->login,$this->issuesByUser)){				$this->users[$issue->_issue->assignee->login]=$issue->_issue->assignee;				$this->issuesByUser[$issue->_issue->assignee->login]=array();				$this->issuesByUser[$issue->_issue->assignee->login][]=$issue;			}else{				$this->issuesByUser[$issue->_issue->assignee->login][]=$issue;			}		}				// TODO have this handle feature and milestone modes		// sort the user issues by feature priority		foreach($this->issuesByUser as $key => $val){			//usort($val,'cmp_milestone_priority');			//usort($val,'cmp_issue_priority');			usort($val,'cmp_tag_priority');			$this->issuesByUser[$key]=$val;		}		// Calculate finish times for each user		foreach($this->issuesByUser as $key => $val){			updateIssueTimes($val);		}		// Organize the issues by milestone		$this->milestones = array();		$this->issuesByMilestone = array();		foreach($issue_objects as $issue){			if(isset($issue->_issue->milestone)){				if(!array_key_exists($issue->_issue->milestone->title,$this->issuesByMilestone)){					$this->milestones[$issue->_issue->milestone->title]=$issue->_issue->milestone;					$this->issuesByMilestone[$issue->_issue->milestone->title]=array();					$this->issuesByMilestone[$issue->_issue->milestone->title][]=$issue;				}else{					$this->issuesByMilestone[$issue->_issue->milestone->title][]=$issue;				}			}else{				$this->issuesByMilestone['unassigned'][]=$issue;			}		}		// sort the milestone issues by finish date desc		foreach($this->issuesByMilestone as $key => $val){			usort($val,'cmp_finish_date');			$this->issuesByMilestone[$key]=$val;		}				// Organize the issues by feature		$this->features = $this->db->getFeatures();		$this->issuesByFeature = array();		foreach($issue_objects as $issue){			if(sizeof($issue->features)>0){				foreach($issue->features as $feature){					if(!array_key_exists($feature->id,$this->issuesByFeature)){						$this->issuesByFeature[$feature->id]=array();						$this->issuesByFeature[$feature->id][]=$issue;					}else{						$this->issuesByFeature[$feature->id][]=$issue;					}				}			}else{				if(!array_key_exists(1,$this->issuesByFeature)){					$this->issuesByFeature[1]=array();				}				$this->issuesByFeature[1][]=$issue;			}		}		// sort the feature issues by finish date desc		foreach($this->issuesByFeature as $key => $val){			usort($val,'cmp_finish_date');			$this->issuesByFeature[$key]=$val;		}				ksort($this->issuesByUser);	}}