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

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->httpClient = new GuzzleHttp\Client();
    }

    /**
     * @When /^I request the home page$/
     */
    public function iRequestTheHomePage()
    {
        try {
            $this->response = $this->httpClient->get('http://pdizz.github.io/');
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->request = $e->getRequest();
            if ($e->hasResponse()) {
                $this->response = $e->getResponse();
            }
        }
    }

    /**
     * @Then /^I should get a successful response$/
     */
    public function iShouldGetASuccessfulResponse()
    {
        Assert::assertNotNull(
            $this->response,
            'Request did not receive any response, unable to get status code.'
        );

        $code = $this->response->getStatusCode();
        Assert::assertEquals(200, $code, "Unexpected response code: $code");
    }
}