InstaBam
=========
This is Symfony project created on November 17, 2018, 5:49 pm.

Requirements
------------

  * PHP language level 5.5;
  * PHP CLI interpreter 7.2.1
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements][2].


Installation
------------

Install the project and run this command:

```bash
composer install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:update
php bin/console doctrine:schema:update --force
```

Run
---

Run `php bin/console server:run`.

Alternatively, you can [configure a web server][3] like Nginx or Apache to run
the application.



