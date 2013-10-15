DROP TABLE IF EXISTS users;
CREATE TABLE users (
   id	    INTEGER AUTO_INCREMENT NOT NULL,
   name    CHAR(20) NOT NULL,
   password    CHAR(40) NOT NULL,
   email       CHAR(40) NOT NULL CHECK (email LIKE '%@%.%'),
   PRIMARY KEY (id)
);

DROP TABLE IF EXISTS user_ratings;
CREATE TABLE user_ratings (
   user_id     INTEGER NOT NULL,
   rating      INTEGER NOT NULL CHECK (rating < 6),
   PRIMARY KEY (user_id,rating)
);

DROP TABLE IF EXISTS user_interests;
CREATE TABLE user_interests (
   user_id     INTEGER NOT NULL,
   course_id   INTEGER NOT NULL,
   PRIMARY KEY (user_id,course_id)
);

DROP TABLE IF EXISTS user_completions;
CREATE TABLE user_completions (
   user_id     INTEGER NOT NULL,
   course_id   INTEGER NOT NULL,
   completed   DATETIME,
   PRIMARY KEY (user_id,course_id)
);

DROP TABLE IF EXISTS user_reviews;
CREATE TABLE user_reviews (
   user_id     INTEGER NOT NULL,
   course_id   INTEGER NOT NULL,
   review      TEXT NOT NULL,
   PRIMARY KEY (user_id,course_id)
);

DROP TABLE IF EXISTS user_log;
CREATE TABLE user_log ( 
   user_id     INTEGER NOT NULL,
   recorded    DATETIME,
   action      ENUM( 'created','search', 'view_course','view_material',
		     'completed','interest','review','rate'),
   search_text TEXT,
   course_id   INTEGER,
   material_id INTEGER
);
      
