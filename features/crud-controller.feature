Feature: Default CRUD controller
    In order to provide default CRUD operations
    As a developer
    I need rad bundle to provide a default CRUD controller

    Scenario: use provided controller
        Given I write in "App/Resources/config/routing.yml":
        """
        App:Foo:
            controller: app.rad_bundle.controller.crud_controller
            defaults:
                _view: App:Foo:show
                _resources:
                    newObject: { service: service_container, method: get, arguments: [value: 'router'] }
                    object:    { service: service_container, method: get, arguments: [value: 'router'] }
                    newForm:   { service: knp_rad.form.manager, method: createBoundObjectForm, arguments: [name: newObject] }
                    form:      { service: knp_rad.form.manager, method: createBoundObjectForm, arguments: [name: object] }
        """
        When I go to "/foo/1"
        Then I should see text matching "The view .*App:Foo:show.html.twig.* is missing"
