Feature: Configure routes using service tags
    In order to normalize the way routes are created and to facilitate route/controller mapping
    As a RAD developer
    I need rad bundle to use tags on controller services

    Background:
        Given I write in "App/Controller/Index.php":
        """
        <?php namespace App\Controller {
            class Index {
                public function __invoke() { return new \Symfony\Component\HttpFoundation\Response('the index page!'); }
            }
        }
        """

    Scenario: generate common routes
        Given I write in "App/Resources/config/services.yml":
        """
        services:
            app.index:
                class: App\Controller\Index
                tags:
                    - {name: knp_rad.route, methods: GET, path: '/test'}
        """
        When I visit "app.index" page
        Then I should see "the index page!"

    Scenario: use expression language as config
        Given I write in "App/Resources/config/services.yml":
        """
        services:
            app.index:
                class: App\Controller\Index
                tags:
                    -
                        name: knp_rad.route
                        expr: '{ "path": "/test", "methods": ["GET", "POST"], "defaults": {"test": "test"} }'

        """
        When I visit "app.index" page
        Then I should see "the index page!"

    Scenario: use yaml as config
        Given I write in "App/Resources/config/services.yml":
        """
        services:
            app.index:
                class: App\Controller\Index
                tags:
                    -
                        name: knp_rad.route
                        yaml: '{ path: /test, methods: [GET, POST], defaults: {test: test} }'

        """
        When I visit "app.index" page
        Then I should see "the index page!"
