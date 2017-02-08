Feature: pdizz.github.io site
    As a developer
    I need a blog
    so I can tell people about stuff

    Scenario: pdizz.github.io home page is available
        When I request "http://pdizz.github.io/"
        Then I should get a "200" response
