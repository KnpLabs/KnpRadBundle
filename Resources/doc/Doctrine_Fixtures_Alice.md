Using Doctrine Fixtures with Alice
==================================

##Load Alice fixtures

```bash
php app/console doctrine:fixtures:load
```

##Add your own Alice fixtures

You just have to add your *.yml* files in *src/YourBundle/Resources/doctrine/orm/*.

##Register your own Alice providers

You just have to tag your service with "*alice.provider*"

```yml
#Resources/config/services.yml
services:
    my.alice.provider:
        class: MyBundle\Alice\MyProvider
        tags:
            - { name: alice.provider }
```
