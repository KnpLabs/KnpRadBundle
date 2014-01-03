Feature: Configure routes using conventions
    In order to normalize the way routes are created
    As a RAD developer
    I need rad bundle to provide a routing loader

    Background:
        Given I write in "App/Controller/RoutingController.php":
        """
        <?php namespace App\Controller {
            class RoutingController {
                public function indexAction() {}
            }
        }
        """

    Scenario: generate common routes
        Given I write in "App/Resources/config/rad.yml":
        """
        App:Routing:
            defaults: { _format: html }
        """
        When I visit "app_routing_index" page
        Then I should see "App:Routing:index.html.twig"

    Scenario: generate nested routes
        Given I write in "App/Controller/Routing/SubController.php":
        """
        <?php namespace App\Controller\Routing {
            class SubController {
                public function indexAction() {}
            }
        }
        """
        And I write in "App/Resources/config/rad.yml":
        """
        App:Routing: ~
        App:Sub:
            defaults: { _format: html }
            parent: 'App:Routing'
        """
        When I visit "app_routing_sub_index" page:
            | routingId | 1 |
        Then I should see "App:Routing/Sub:index.html.twig"
