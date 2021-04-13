### Testing app
The purpose of this app is to process a user creation use case in Slim PHP and then spread an internal message through AMQP protocol in order to send a welcome email with Golang.

### Steps
- Clone
- cd docker && docker-compose up -d
- Send POST request at 10.6.0.2/users with a email & password body.

### You will have to create users table by hand
- docker exec -it db bash
- mysql -uroot -psecret
-  use app;
-  CREATE TABLE users (id CHAR(36), email VARCHAR(320), password CHAR(70));
