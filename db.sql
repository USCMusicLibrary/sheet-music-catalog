/* main table for records*/
CREATE TABLE records (
  id int(11) NOT NULL AUTO_INCREMENT,
  mid int(11),
  title text,
  publisher varchar(255),
  call_number varchar(255),
  series varchar(255),
  larger_work varchar(255),
  collection_source varchar(255),
  donor varchar(255),
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

CREATE TABLE years (
  record_id int(11),
  start_year varchar(10),
  end_year varchar(10)
);

CREATE TABLE notes (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11),
  note text,
  PRIMARY KEY (id)
);

CREATE TABLE contributors (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  contributor_id int(11) NOT NULL,
  role_id int(2) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE roles (
  id int(11) NOT NULL AUTO_INCREMENT,
  role varchar(75),
  relatorcode varchar(3),
  PRIMARY KEY (id)
);

CREATE TABLE names (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255),
  uri varchar(100),
  nameUpdate varchar(255),
  problem_note varchar(255),
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
  text_t text,
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
  uri varchar(100),
  subject_heading text,
  subjectUpdate varchar(255),
  problem_note varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE has_subject (
 id int(11) NOT NULL AUTO_INCREMENT,
 record_id int(11) NOT NULL,
 subject_id int(11) NOT NULL,
 PRIMARY KEY(id)
);

CREATE TABLE hidden_subject_headings (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  hidden_subject_heading text,
  PRIMARY KEY (id)
);

CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(255) DEFAULT NULL,
  password_hash varchar(255) DEFAULT NULL,
  user_role varchar(255),
  email varchar(255),
  email_verify tinyint(1),
  request_time datetime DEFAULT "0000-00-00 00:00:00",
  password_uid varchar(255),
  PRIMARY KEY (id)
);


/* sql to delete contents of tables */
/* USE WITH CARE */
truncate table alternative_titles;
truncate table contributors;
truncate table languages;
truncate table names;
truncate table notes;
truncate table publisher_locations;
truncate table records;
truncate table roles;
truncate table subject_headings;
truncate table texts;
truncate table years;
truncate table has_subject;