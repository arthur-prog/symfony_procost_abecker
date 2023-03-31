# symfony_procost_abeckera

### Arthur Becker
arthurbecker57420@gmail.com

## Configuration

Create a .env.local file and set:
```
DATABASE_URL=""
```

To create the database, in a terminal at the root the project, execute:
```
php bin/console doctrine:database:create
```

Compare the database and execute the migrations:
```
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

Load fixtures:
```
php bin/console doctrine:fixtures:load
```

Run the project:
```
cd .\public\
php -S 127.0.0.1:3060
```
