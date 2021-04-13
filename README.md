### Create users table
docker exec -it db bash
mysql -uroot -psecret
use app;
CREATE TABLE users (id CHAR(36), email VARCHAR(320), password CHAR(70));
