# vyapar_guru-volunteer_app-php_mysql
A platform to help small business owners to find volunteers to support them.

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone BIGINT(10) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    type BOOLEAN NOT NULL DEFAULT 0,
    profile_picture VARCHAR(255) UNIQUE,
    about VARCHAR(255)
);

CREATE TABLE skillset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE skillset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user INT NOT NULL,
    exp INT NOT NULL,
    skill INT NOT NULL
);

CREATE TABLE requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user INT NOT NULL,
    skill INT NOT NULL,
    business VARCHAR(255) NOT NULL
);

                