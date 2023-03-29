-- #########################################################
-- POUR INITIALISER LA BASE DE DONNER
-- ALLEZ DANS : /assets/db_init                 POUR LES REGLAGES
-- ALLEZ DANS : /public_root/init_database.php  POUR LANCER L'INITIALISATION

drop database if exists hrp_project;
create database hrp_project;
use hrp_project;

-- ##########################################################
-- CLEAN (attention à bien supprimer les parents après les enfants !!)

drop table if exists direct_messages;   -- REF TO USERS
drop table if exists friends;           -- REF TO USERS

drop table if exists reports;           -- REF TO USERS/POSTS
drop table if exists likes;             -- REF TO USERS/POSTS
drop table if exists posts;             -- REF TO USERS

drop table if exists pages_liked;       -- REF TO USERS

drop table if exists users;

-- ##########################################################
-- USER DATA

CREATE TABLE `users` (
    `id`            bigint UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `cookie_id`     varchar(32)     DEFAULT NULL,
    `token_id`      varchar(32)     DEFAULT NULL,

    `username`      varchar(32)     DEFAULT NULL,
    `password`      varchar(255)    DEFAULT NULL, -- 255 au lieu de 60 en cas d'evolution de la fonction password_hash
    `creation_date` INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),

    `last_join`     INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),
    `last_try`      INT UNSIGNED    DEFAULT (unix_timestamp(CURRENT_TIMESTAMP) - 20),
    `last_reroll`   INT UNSIGNED    DEFAULT (0), 
    
    `cookie_enabled`BOOLEAN         DEFAULT FALSE,     
    `cookie_pass`   varchar(32)     DEFAULT NULL,
    `cookie_expire` INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),
    `token_expire`  INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),

    `enable_public` BOOLEAN         DEFAULT FALSE,
    `memory_public` BOOLEAN         DEFAULT FALSE,          

    `public_image`  INT             DEFAULT 0,
    `public_name`   varchar(32)     DEFAULT NULL,
    `specie`        varchar(32)     DEFAULT NULL,
    `class`         varchar(32)     DEFAULT NULL,
    `title`         varchar(32)     DEFAULT NULL,
    `likes`         int UNSIGNED    DEFAULT 0,
    `description`   varchar(50)     DEFAULT NULL,
    
    `banned`        BOOLEAN         DEFAULT FALSE,
    `banned_to`     INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),
    
    `admin`         BOOLEAN         DEFAULT FALSE

) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE pages_liked (
    `id`           bigint   UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      bigint   UNSIGNED NOT NULL,
    `like_id`      bigint   UNSIGNED NOT NULL,

    `priority`     BOOLEAN  DEFAULT TRUE,

    PRIMARY KEY(id),
    FOREIGN KEY (`user_id`) REFERENCES users(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`like_id`) REFERENCES users(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

-- ##########################################################
-- POSTS DATA

CREATE TABLE `posts` (
    `id`            bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`       bigint UNSIGNED NOT NULL,

    `public_image`  INT             DEFAULT 0,
    `public_name`   varchar(32)     DEFAULT NULL,
    
    `reportnum`     INT             DEFAULT 0,
    `last_report`   INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),
    `creation_date` INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),
    
    `content`       varchar(735)    DEFAULT NULL,
    `like_num`      INT             DEFAULT 0,
    `response_id`   bigint UNSIGNED DEFAULT NULL,

    PRIMARY KEY (id),
    FOREIGN KEY (user_id)      REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (response_id)  REFERENCES posts(id)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE likes (
    `id`               bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `message_id`     bigint UNSIGNED NOT NULL,
    `user_id`        bigint UNSIGNED NOT NULL,

    PRIMARY KEY(id),
    FOREIGN KEY (`message_id`) REFERENCES posts(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)    REFERENCES users(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE reports (
    `id`               bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `message_id`     bigint UNSIGNED NOT NULL,
    `user_id`        bigint UNSIGNED NOT NULL,

    PRIMARY KEY(id),
    FOREIGN KEY (`message_id`) REFERENCES posts(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)    REFERENCES users(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

-- ##########################################################
-- FRIENDS

CREATE TABLE friends (
    `id`               bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id_0`        bigint UNSIGNED NOT NULL,
    `user_id_1`        bigint UNSIGNED NOT NULL,
    `accepted`         BOOLEAN         DEFAULT FALSE,

    PRIMARY KEY(id),
    FOREIGN KEY (`user_id_1`)   REFERENCES users(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id_0`)   REFERENCES users(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

-- ##########################################################
-- DIRECT MESSAGES

CREATE TABLE direct_messages (
    `id`            bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_id`       bigint UNSIGNED NOT NULL,
    `to_id`         bigint UNSIGNED NOT NULL,
    `creation_date` INT UNSIGNED    DEFAULT unix_timestamp(CURRENT_TIMESTAMP),
    `content`       varchar(256)    DEFAULT NULL,
    `private`       BOOLEAN         DEFAULT TRUE, -- Pour differencier les messages de match et d'amis

    PRIMARY KEY(id),
    FOREIGN KEY(from_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(to_id)   REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;