<?php


namespace Romano83\Akismet\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Romano83\Akismet\Akismet;

class AkismetTest extends TestCase
{

    public function getClient($handler)
    {
        return new Client(['handler' => $handler]);
    }

    public function testConstructorWithoutAPIKey()
    {
        $this->expectException(\Exception::class);
        new Akismet(WEBSITE, 'falseAPIKey');
    }

    public function testValidAPIKey()
    {
        new Akismet(WEBSITE, APIKEY);

        $mock = new MockHandler([
            new Response(200, [], 'valid'),
        ]);
        $handler = HandlerStack::create($mock);

        $response = $this->getClient($handler)->request('POST', '1.1/verify-key', [
            'timeout' => 10,
            'form_params' => [
                'blog' => urlencode(WEBSITE),
                'key' => APIKEY
            ]
        ]);
        $this->assertEquals('valid', $response->getBody()->getContents());
    }

    public function testInvalidAPIKey()
    {
        new Akismet(WEBSITE, APIKEY);

        $mock = new MockHandler([
            new Response(200, [], 'invalid'),
        ]);
        $handler = HandlerStack::create($mock);

        $response = $this->getClient($handler)->request('POST', '1.1/verify-key', [
            'timeout' => 10,
            'form_params' => [
                'blog' => urlencode(WEBSITE),
            ]
        ]);
        $this->assertNotEquals('valid', $response->getBody()->getContents());
    }

    public function testIsCommentSpam()
    {
        $mock = $this->getMockBuilder(Akismet::class)
             ->setConstructorArgs([ \WEBSITE, \APIKEY ])
             ->setMethods([ 'isCommentSpam' ])
             ->getMock();

        $mock->expects($this->once())
             ->method('isCommentSpam')
             ->will($this->returnValue(true));

        $mock->setUserIP('127.0.0.1')
            ->setCommentAuthor('viagra-test-123')
            ->setReferrer('https://www.google.com')
            ->setUserAgent(
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6'
            )
            ->setCommentAuthorEmail('example@gmail.com')
            ->setCommentAuthorURL('https://www.example.com')
            ->setPermalink(WEBSITE . '/test-url')
            ->setCommentType('blog')
            ->setCommentContent(
                'Interneciva regis civitates pristinae pristinae Seleucia vestigia coloniam subversa coloniam.'
            );

        $this->assertTrue($mock->isCommentSpam());
    }

    public function testIsNotSpam()
    {
        $mock = $this->getMockBuilder(Akismet::class)
            ->setConstructorArgs([ \WEBSITE, \APIKEY ])
            ->setMethods([ 'isCommentSpam' ])
            ->getMock();

        $mock->expects($this->once())
             ->method('isCommentSpam')
             ->will($this->returnValue(false));

        $mock->setUserIP('127.0.0.1')
            ->setCommentAuthor('John Doe')
            ->setReferrer('https://www.google.com')
            ->setUserAgent(
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6'
            );
        $this->assertFalse($mock->isCommentSpam());
    }

    public function testSubmitSpam()
    {
        $mock = $this->getMockBuilder(Akismet::class)
            ->setConstructorArgs([ \WEBSITE, \APIKEY ])
            ->setMethods([ 'submitSpam' ])
            ->getMock();

        $mock->expects($this->once())
             ->method('submitSpam')
             ->will($this->returnValue(true));

        $mock->setUserIP('127.0.0.1')
            ->setCommentAuthor('John Doe')
            ->setReferrer('https://www.google.com')
            ->setUserAgent(
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6'
            );
        $this->assertTrue($mock->submitSpam());
    }

    public function testSubmitHam()
    {
        $mock = $this->getMockBuilder(Akismet::class)
                     ->setConstructorArgs([ \WEBSITE, \APIKEY ])
                     ->setMethods([ 'submitHam' ])
                     ->getMock();

        $mock->expects($this->once())
             ->method('submitHam')
             ->will($this->returnValue(true));

        $mock->setUserIP('127.0.0.1')
            ->setCommentAuthor('John Doe')
            ->setReferrer('https://www.google.com')
            ->setUserAgent(
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6'
            );
        $this->assertTrue($mock->submitHam());
    }
}
