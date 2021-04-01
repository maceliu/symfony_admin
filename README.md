# symfony_admin
A admin system base on symfony 5.*

## How to use it ?

### 1.Install by composer
```
composer require maceliu/symfony_admin
```

### 2.Add routes config 
config/routes/annotations.yaml
```
symfony_admin:
    resource: ../../vendor/maceliu/symfony_admin/src/Controller/
    type: annotation
```

### 3. Add service config
config/service.yaml

```
parameters:

services:
    # .....your other config.......
    
    SymfonyAdmin\:
        resource: '../vendor/maceliu/symfony_admin/src/*'
        exclude: '../vendor/maceliu/symfony_admin/src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}'

    SymfonyAdmin\Controller\:
        resource: '../vendor/maceliu/symfony_admin/src//Controller'
        tags: ['controller.service_arguments']
        
```

### 4. Install db data

Execute the SQL statements in the install.sql in your database