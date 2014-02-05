Feature: Auto detection of entities and repository service
    In order to make my life easier
    As a developer
    I need rad bundle to register doctrine repository DI services automatically

    Scenario: Add an entity
        Given I write in "App/Entity/BlogPost.php":
        """
        <?php

        namespace App\Entity;

        /** @Doctrine\ORM\Mapping\Entity **/
        class BlogPost
        {
            /**
             * @Doctrine\ORM\Mapping\Id
             * @Doctrine\ORM\Mapping\GeneratedValue
             * @Doctrine\ORM\Mapping\Column(type="bigint")
             **/
            public $id;
        }
        """
        And I write in "App/Listener.php":
        """
        <?php namespace App {
            class Listener
            {
                public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
                {
                    $this->container = $container;
                }

                public function onStuff() {
                    var_dump('ok');
                }
            }
        }
        """
        And I write in "App/Resources/config/services.yml":
        """
        services:
            app.listener:
                class: App\Listener
                arguments: ['@service_container']
                tags:
                    - { name: doctrine.event_listener, event: onStuff }
        """
        Then "app.listener" should be a registered service
