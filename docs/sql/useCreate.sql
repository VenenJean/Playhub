-- Create a new SQL Server login (server-level)
CREATE LOGIN php_user
WITH
    PASSWORD = 'StrongPassword123!';

USE playhub;

CREATE USER php_user FOR LOGIN php_user;

-- Basic read/write access
ALTER ROLE db_datareader ADD MEMBER php_user;

ALTER ROLE db_datawriter ADD MEMBER php_user;

ALTER ROLE db_ddladmin ADD MEMBER php_user;