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

GRANT ALL ON * TO enhancedgi@'localhost' IDENTIFIED BY 'poiulkjh';

INSERT INTO features(title,description,priority) VALUES('unassigned','',10000000);