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
     * @When /^I request "(.+)"$/
     */
    public function iRequest($url)
    {
        try {
            $this->response = $this->httpClient->get($url);
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
    public function iShouldGetAResponse($httpStatusCode)
    {
        Assert::assertNotNull(
            $this->response,
            'Request did not receive any response, unable to get status code.'
        );

        $code = $this->response->getStatusCode();
        Assert::assertEquals(
            $httpStatusCode,
            $code,
            "Unexpected response code: $code"
        );
    }
}
