drop database if exists pubtex;
drop database if exists pubtex_dev;
drop database if exists pubtex_test;

create database if not exists pubtex;
create database if not exists pubtex_dev;
create database if not exists pubtex_test;

use pubtex;

/* --------------------------- */
/*            USERS            */
/* --------------------------- */
create table if not exists users
(
	id int(11) not null auto_increment primary key,
	username varchar(50) unique not null,
	password varchar(50) not null,
	salt varchar(50) not null,
	role varchar(50) not null,
	real_name varchar(150) not null,
	created datetime not null
) ENGINE=InnoDB;

/* --------------------------- */
/*            TYPES            */
/* --------------------------- */
create table if not exists types
(
	id int not null auto_increment primary key,
	name varchar(20) unique not null,		
	created datetime not null
) ENGINE=InnoDB;

create table if not exists prefixes
(
	id int not null auto_increment primary key,
	prefix varchar(10) unique not null,
	mime varchar(40) not null,
	created datetime not null
) ENGINE=InnoDB; 

create table if not exists type_prefix
(
	types_id int not null,
	prefix_id int not null,
	foreign key (types_id) references types(id),
	foreign key (prefix_id) references prefixes(id)
) ENGINE=InnoDB;

create table if not exists params
(
	id int not null auto_increment primary key,
	name varchar(40) unique not null
) ENGINE=InnoDB;

create table if not exists type_param
(
	types_id int not null,
	param_id int not null,
	foreign key (types_id) references types(id),
	foreign key (param_id) references params(id)	
) ENGINE=InnoDB;

/* --------------------------- */
/*           TAGS              */
/* --------------------------- */
create table if not exists tags
(
	id int not null auto_increment primary key,
	tag varchar(50) unique not null,	
	created datetime not null
) ENGINE=InnoDB;

/* --------------------------- */
/*            MEDIA            */
/* --------------------------- */
create table if not exists media
(
	id int not null auto_increment primary key,
	hash_name varchar(50) unique,
	description text,
	media_type int not null,
	user_id int not null,
	created datetime not null,	
	foreign key (user_id) references users(id),
	foreign key (media_type) references types(id)
) ENGINE=InnoDB;
create index id on media (id);

create table if not exists media_tag
(
	media_id int not null,
	tag_id int not null,
	foreign key (media_id) references media(id),
	foreign key (tag_id) references tags(id)
);

/* --------------------------- */
/*           FILES             */
/* --------------------------- */
create table if not exists files
(
	id int not null auto_increment primary key,
	hash_name varchar(50) unique,
	prefix int not null,
	user_id int not null,
	created datetime not null,	
	foreign key (user_id) references users(id),
	foreign key (prefix) references prefixes(id)
) ENGINE=InnoDB;

create table if not exists file_tag
(
	file_id int not null,
	tag_id int not null,
	foreign key (file_id) references files(id),
	foreign key (tag_id) references tags(id)
) ENGINE=InnoDB;

create table if not exists media_file
(
	version int not null,
	media_id int not null,
	file_id int not null,
	foreign key(media_id) references media(id),
	foreign key(file_id) references files(id)
) ENGINE=InnoDB;

create table if not exists details
(
	key varchar(50),
	kval varchar(255),
	file_id int not null,
	foreign key (file_id) references files(id)
) ENGINE=InnoDB;


/* --------------------------- */
/*          DOWNLOADS          */
/* --------------------------- */
create table if not exists fdnl
(
	stamp datetime,
	user_id int not null,
	file_id int not null,
	foreign key(user_id) references users(id),
	foreign key(file_id) references files(id)
) ENGINE=InnoDB;

/* --------------------------- */
/*        DOWNLOAD GROUP       */
/* --------------------------- */
create table if not exists dnlgrp
(
	id int not null auto_increment primary key,
	stamp datetime not null,
	user_id int not null,
	name varchar(50),
	foreign key(user_id) references users(id)
) ENGINE=InnoDB;

create table if not exists dnlgrp_file
(
	dnlgrp_id int not null,
	file_id int not null,
	foreign key(dnlgrp_id) references dnlgrp(id),
	foreign key(file_id) references files(id)
) ENGINE=InnoDB;

/* --------------------------- */
/*           RATING            */
/* --------------------------- */
create table if not exists rate
(
	user_id int not null,
	file_id int not null,
	value int not null,
	foreign key(user_id) references users(id),
	foreign key(file_id) references files(id)
);

/* --------------------------- */
/*        PRIVILEGES           */
/* --------------------------- */
grant all privileges on pubtex.* to mike@'localhost' identified by '123456';

