DROP TABLE IF EXISTS features_issues;
DROP TABLE IF EXISTS features;
CREATE TABLE features(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
	priority INT NOT NULL DEFAULT 0
) ENGINE=INNODB;

CREATE TABLE features_issues(
    feature_id INTEGER NOT NULL,
	issue_repo VARCHAR(64) NOT NULL,
    issue_id INTEGER NOT NULL,
    FOREIGN KEY (feature_id) REFERENCES features(id)  ON DELETE CASCADE
) ENGINE=INNODB;

DROP TABLE IF EXISTS issue_priority;
CREATE TABLE issue_priority(
	issue_repo VARCHAR(64) NOT NULL,
    issue_id INTEGER NOT NULL,
	priority INTEGER NOT NULL,
	UNIQUE INDEX repo_ticket_index (issue_repo,issue_id)
) ENGINE=INNODB;

DROP TABLE IF EXISTS dev_availability;
CREATE TABLE `enhancedgi`.`dev_availability`(
  `developer_name` VARCHAR(64) NOT NULL,
  `available_days_per_week` INT NOT NULL,
  `effective_date` DATETIME
);

ALTER TABLE `enhancedgi`.`issue_priority`   
  ADD COLUMN `milestone_id` INT(11) NULL AFTER `tag_priority`;
  
GRANT ALL ON * TO enhancedgi@'localhost' IDENTIFIED BY 'poiulkjh';

INSERT INTO features(title,description,priority) VALUES('unassigned','',10000000);