<?php

require ('../lib/db.php'); 

$db=new Database();


$db->createIssuePriority("test",1,"tester",1,1);
$db->createIssuePriority("test",2,"tester",1,1);
$db->createIssuePriority("test",3,"tester",1,1);
$db->createIssuePriority("test",4,"tester",1);

//$db->updateIssuePriority("test",4,10);

//$db->swapPrioritys("test",1,"test",2);

//$db->upPriority("test",4);

$db->downPriority("test",2);
$db->downPriority("test",2);
$db->downPriority("test",2);
$db->downPriority("test",2);

$db->upPriorityMs("test",2);