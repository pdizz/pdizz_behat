# features/client-api.feature
Feature: Client REST API
    As a guy who does business stuff
    I need a client API
    so I can keep track of my many clients

    Scenario: I get my first client
        When I request "GET /client/1"
        Then I should get a "200" response
        And the response should contain ""id": 1"
        And the "firstName" field should be "John"
        And the "lastName" field should be "Doe"
        And the "address" field should exist
        And the "address.state" field should be "CO"

    Scenario: I add my second client
        Given a request body "{"id":2,"firstName":"Jane","lastName":"Doe","address":{"street":"123 Main St","city":"Denver","state":"CO"}}"
        When I request "POST /client"
        Then the "id" field should be "2"
        And the "firstName" field should be "Jane"
        And the "lastName" field should be "Doe"

    Scenario: I fix a typo in my client's name
        Given a request body with:
        """
        {
            "firstName":"Jayne",
            "lastName":"Doe",
            "address": {
                "street":"123 Main St",
                "city":"Denver",
                "state":"CO"
            }
        }
        """
        When I request "PUT /client/2"
        Then the "id" field should be "2"
        And the "firstName" field should be "Jayne"
        And the "lastName" field should be "Doe"

    Scenario: I delete my second client
        When I request "DELETE /client/2"
        Then I should get a "200" response
