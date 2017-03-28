<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected $httpClient;

    /** @var \GuzzleHttp\Psr7\Request */
    protected $request;

    /** @var \GuzzleHttp\Psr7\Response */
    protected $response;

    /** @var string */
    protected $baseUrl;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($baseUrl)
    {
        $this->httpClient = new GuzzleHttp\Client();
        $this->baseUrl = $baseUrl;
    }

    /**
     * @When /^I request "(.+)"$/
     */
    public function iRequest($route)
    {
        try {
            $this->response = $this->httpClient->get($this->baseUrl . $route);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->request = $e->getRequest();
            if ($e->hasResponse()) {
                $this->response = $e->getResponse();
            }
        }
    }

    /**
     * @Then /^I should get a "(.*)" response$/
     */
    public function iShouldGetAResponse($expectedCode)
    {
        Assert::assertNotNull(
            $this->response,
            'Request did not receive any response, unable to get status code.'
        );

        $actualCode = $this->response->getStatusCode();
        Assert::assertEquals(
            $expectedCode,
            $actualCode,
            "Unexpected response code: $actualCode"
        );
    }
}