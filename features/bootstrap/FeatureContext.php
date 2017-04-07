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

    /** @var string */
    protected $requestBody;

    /** @var string */
    protected $responseBody;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = new GuzzleHttp\Client([
            'base_uri' => $baseUrl
        ]);
    }

    /**
     * @When /^I request "(.+) (.+)"$/
     */
    public function iRequest($verb, $route)
    {
        $options['headers'] = [
            'Content-Type' => 'application/json'
        ];

        $options['body'] = $this->requestBody;

        try {
            $this->response = $this->httpClient->request($verb, $route, $options);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->request = $e->getRequest();
            if ($e->hasResponse()) {
                $this->response = $e->getResponse();
            }
        } finally {
            if (isset($this->response)) {
                $this->responseBody = $this->response->getBody()->getContents();
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

    /**
     * @Then /^the response should contain "(.*)"$/
     */
    public function theResponseShouldContain($string)
    {
        Assert::assertContains($string, $this->responseBody);
    }

    /**
     * @Given /^the "(.*)" field should exist$/
     */
    public function theFieldShouldExist($fieldPath)
    {
        $body = json_decode($this->responseBody, true);
        $fields = explode('.', $fieldPath);

        $path = [];

        while (count($fields) != 0) {
            $field = array_shift($fields);
            $path[] = $field;
            Assert::assertArrayHasKey(
                $field,
                $body,
                "Unable to find field " . join('.', $path) . PHP_EOL . $this->responseBody
            );

            $body = $body[$field];
        }

        return $body;
    }

    /**
     * @Given /^the "(.*)" field should be "(.*)"$/
     */
    public function theFieldShouldBe($fieldPath, $value)
    {
        $body = $this->theFieldShouldExist($fieldPath);

        Assert::assertEquals(
            $value,
            $body,
            "The $$fieldPath field did not contain the expected value $value. It was " . $body . PHP_EOL . $this->responseBody
        );
    }

    /**
     * @Given /^a request body "(.*)"$/
     */
    public function aRequestBody($string)
    {
        $this->requestBody = (string) $string;
    }

    /**
     * @Given /^a request body with:$/
     */
    public function aRequestBodyWith(PyStringNode $string)
    {
        $this->requestBody = (string) $string;
    }
}