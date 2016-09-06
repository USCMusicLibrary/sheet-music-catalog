/* main table for records*/
CREATE TABLE records (
  id int(11) NOT NULL AUTO_INCREMENT,
  title text,
  publisher int(11),
  year varchar(255),
  call_number varchar(255),
  series varchar(255),
  larger_work varchar(255),
  collection_source varchar(255),
  donor text varchar(255),
  notes text varchar(255),
  scanning_technician varchar(255),
  media_cataloguer varchar(255),
  reviewer varchar(255),
  PRIMARY KEY (id)
);

/*
 * additional tables
 */
CREATE TABLE alternative_titles (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  alternative_title text,
  PRIMARY KEY (id)
);

CREATE TABLE contributors (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  contributor varchar(255),
  role varchar(255),
  /* This is where we would store the contributor URI if we have one */
  contributor_uri text,
  PRIMARY KEY (id)
);

CREATE TABLE publisher_locations (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  publisher_location varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE texts (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  text text,
  PRIMARY KEY (id)
);

CREATE TABLE languages (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  language varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE subject_headings (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  subject_heading text,
  PRIMARY KEY (id)
);

CREATE TABLE hidden_subject_headings (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  hidden_subject_heading text,
  PRIMARY KEY (id)
);
