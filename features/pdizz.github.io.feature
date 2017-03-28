Feature: pdizz.github.io site
    As a developer
    I need a blog
    so I can tell people about stuff

    Scenario: I visit the home page
        When I request "/"
        Then I should get a "200" response

    Scenario: I view the Behat article
        When I request "/testing/2017/03/22/behavioral-api-testing-with-behat-01.html"
        Then I should get a "200" response

