# vyapar_guru-volunteer_app-php_mysql
A platform to help small business owners to find volunteers to support them.

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone BIGINT(10) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    type BOOLEAN NOT NULL DEFAULT 0
);