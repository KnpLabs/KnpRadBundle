Feature: disable auto detections
    In order to keep control
    As a developer
    I need rad bundle to disable auto detection

    Scenario: disable entity repository detection
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
        And I write in "config.yml":
        """
        knp_rad:
            detect:
                entity: false
                form_creator: false
                form_extension: false
                form_type: false
                twig: false
                security_voter: false
                validator_constraint: false
        """
        Then "app.entity.blog_post_repository" should not be a registered service
