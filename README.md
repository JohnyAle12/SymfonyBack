## About Project

This is a Rest API application where i put some knowledges about PHP and Symfony framework, here i build a basic api service to create and list users using JWT authenticator.

## Start Project

Before you download project, please run:

```bash
# install vendor packages
$ composer install
```

After configure your .env file according your database enviroment and according .env.example.

For generate the new ssl keys to jwt

```bash
# generate new ssl keys on the .env file
$ php bin/console lexik:jwt:generate-keypair
```

After that you can start the application in local with:

```bash
# start the virtual server and run application
$ symfony server:start
```


