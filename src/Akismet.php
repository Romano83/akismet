<?php


namespace Romano83\Akismet;

use GuzzleHttp\Client;

class Akismet
{

    /**
     * @var string
     */
    private $akismetServer = 'https://rest.akismet.com';

    /**
     * @var string
     */
    private $akismetVersion = '1.1';

    /**
     * Http Client
     * @var Client GuzzleHttp\Client
     */
    private $client;

    /**
     * Website or blog URL
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $apiKey;
    
    /**
     * Comment to send to Akismet web service.
     * blog, user_ip and user_agent are required properties
     *
     * @var array
     */
    private $comment = [];

    /**
     * Akismet constructor.
     *
     * @param string    $website   Must be a full URI including http(s)://
     * @param string    $apiKey
     * @throws \Exception
     */
    public function __construct(string $website, string $apiKey)
    {
        $this->website = $website;
        $this->apiKey = $apiKey;
        $this->comment['blog'] = $website;

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->comment['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->comment['referrer'] = $_SERVER['HTTP_REFERER'];
        }
        if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
            $this->comment['user_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        $this->client = new Client(['base_uri' => $this->akismetServer]);
        if (!$this->isValidAPIKey()) {
            throw new \Exception(
                'The API key passed in Akismet constructor is invalid.' .
                'Please obtain a valid one from https://akismet.com'
            );
        }
    }

    /**
     * Submit content to {@link http://www.akismet.com Akismet} web service
     * to see whether or not the submitted comment is spam
     *
     * @return bool
     */
    public function isCommentSpam() : bool
    {
        $response = $this->client->request(
            'POST',
            'https://' . $this->apiKey . '.rest.akismet.com/' . $this->akismetVersion . '/comment-check',
            [
                'timeout' => 15,
                'form_params' => [
                    'blog' => $this->comment['blog'],
                    'user_ip' => $this->comment['user_ip'],
                    'user_agent' => $this->comment['user_agent'],
                    'referrer' => $this->comment['referrer'] ?? null,
                    'comment_author' => $this->comment['comment_author'] ?? null,
                    'comment_author_email' => $this->comment['comment_author_email'] ?? null,
                    'comment_author_url' => $this->comment['comment_author_url'] ?? null,
                    'comment_type' => $this->comment['comment_type'] ?? null,
                    'comment_content' => $this->comment['comment_content'] ?? null,
                    'permalink' => $this->comment['permalink'] ?? null,
                    'comment_date_gmt' => $this->comment['comment_date_gmt'] ?? null,
                    'comment_post_modified_gmt' => $this->comment['comment_post_modified_gmt'] ?? null,
                    'blog_lang' => $this->comment['blog_lang'] ?? null,
                    'blog_charset' => $this->comment['blog_charset'] ?? null,
                    'user_role' => $this->comment['user_role'] ?? null,
                    'is_test' => $this->comment['is_test'] ?? null
                ],
            ]
        );

        $responseContent = $response->getBody()->getContents();

         return 'true' == $responseContent;
    }

    /**
     * Submit spam that is incorrectly tagged as ham
     *
     * @return bool
     */
    public function submitSpam() : bool
    {
        $response = $this->client->request(
            'POST',
            'https://'.$this->apiKey.'.rest.akismet.com/'.$this->akismetVersion.'/submit-spam',
            [
                'timeout' => 10,
                'form_params' => [
                    'blog' => $this->comment['blog'],
                    'user_ip' => $this->comment['user_ip'],
                    'user_agent' => $this->comment['user_agent'],
                    'referrer' => $this->comment['referrer'] ?? null,
                    'comment_author' => $this->comment['comment_author'] ?? null,
                    'comment_author_email' => $this->comment['comment_author_email'] ?? null,
                    'comment_author_url' => $this->comment['comment_author_url'] ?? null,
                    'comment_type' => $this->comment['comment_type'] ?? null,
                    'comment_content' => $this->comment['comment_content'] ?? null,
                    'permalink' => $this->comment['permalink'] ?? null,
                    'comment_date_gmt' => $this->comment['comment_date_gmt'] ?? null,
                    'comment_post_modified_gmt' => $this->comment['comment_post_modified_gmt'] ?? null,
                    'blog_lang' => $this->comment['blog_lang'] ?? null,
                    'blog_charset' => $this->comment['blog_charset'] ?? null,
                    'user_role' => $this->comment['user_role'] ?? null,
                    'is_test' => $this->comment['is_test'] ?? null
                ]
            ]
        );
        return 'Thanks for making the web a better place.' == $response->getBody()->getContents();
    }

    /**
     * Submit ham that is incorrectly tagged as spam
     *
     * @return bool
     */
    public function submitHam() : bool
    {
        $response = $this->client->request(
            'POST',
            'https://'.$this->apiKey.'.rest.akismet.com/'.$this->akismetVersion.'/submit-ham',
            [
                'timeout' => 10,
                'form_params' => [
                    'blog' => $this->comment['blog'],
                    'user_ip' => $this->comment['user_ip'],
                    'user_agent' => $this->comment['user_agent'],
                    'referrer' => $this->comment['referrer'] ?? null,
                    'comment_author' => $this->comment['comment_author'] ?? null,
                    'comment_author_email' => $this->comment['comment_author_email'] ?? null,
                    'comment_author_url' => $this->comment['comment_author_url'] ?? null,
                    'comment_type' => $this->comment['comment_type'] ?? null,
                    'comment_content' => $this->comment['comment_content'] ?? null,
                    'permalink' => $this->comment['permalink'] ?? null,
                    'comment_date_gmt' => $this->comment['comment_date_gmt'] ?? null,
                    'comment_post_modified_gmt' => $this->comment['comment_post_modified_gmt'] ?? null,
                    'blog_lang' => $this->comment['blog_lang'] ?? null,
                    'blog_charset' => $this->comment['blog_charset'] ?? null,
                    'user_role' => $this->comment['user_role'] ?? null,
                    'is_test' => $this->comment['is_test'] ?? null
                ]
            ]
        );
        return 'Thanks for making the web a better place.' == $response->getBody()->getContents();
    }

    /**
     * Check if API key is valid
     *
     * @return bool
     */
    private function isValidAPIKey() : bool
    {
        $response = $this->client->request('POST', $this->akismetVersion.'/verify-key', [
            'timeout' => 10,
            'form_params' => [
                'blog' => urlencode($this->website),
                'key' => $this->apiKey
            ]
        ]);
        return 'valid' == $response->getBody()->getContents();
    }

    /**
     * Lets you override the user agent used to submit the comment.
     * you may wish to do this when submitting ham/spam.
     * Required.
     *
     * Defaults to $_SERVER['HTTP_USER_AGENT']
     *
     * @param $userAgent    string
     * @return $this
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->comment['user_agent'] = $userAgent;
        return $this;
    }

    /**
     * To override the user IP address when submitting spam/ham later on.
     * Required
     *
     * Default to $_SERVER['REMOTE_ADDR']
     *
     * @param string $userIp    An IP address.
     * @return $this
     */
    public function setUserIP(string $userIp) :self
    {
        $this->comment['user_ip'] = $userIp;
        return $this;
    }

    /**
     * To override the referring page when submitting spam/ham later on
     *
     * Default to $_SERVER['HTTP_REFERER']
     *
     * @param string $referrer  The referring page.
     * @return $this
     */
    public function setReferrer(string $referrer) :self
    {
        $this->comment['referrer'] = $referrer;
        return $this;
    }

    /**
     * A permanent URL referencing the blog post the comment was submitted to.
     *
     * @param string $permalink The URL.  Optional.
     * @return $this
     */
    public function setPermalink(string $permalink) :self
    {
        $this->comment['permalink'] = $permalink;
        return $this;
    }

    /**
     * The type of comment being submitted.
     *
     * May be blank, comment, trackback, pingback, or a made up value like "registration" or "wiki".
     *
     * @param $commentType  string
     * @return $this
     */
    public function setCommentType(string $commentType) :self
    {
        $this->comment['comment_type'] = $commentType;
        return $this;
    }

    /**
     * The name that the author submitted with the comment.
     *
     * @param $commentAuthor    string
     * @return $this
     */
    public function setCommentAuthor(string $commentAuthor) :self
    {
        $this->comment['comment_author'] = $commentAuthor;
        return $this;
    }

    /**
     * The email address that the author submitted with the comment.
     * The address is assumed to be valid.
     *
     * @param $authorEmail  string
     * @return $this
     */
    public function setCommentAuthorEmail(string $authorEmail) :self
    {
        $this->comment['comment_author_email'] = $authorEmail;
        return $this;
    }

    /**
     * The URL that the author submitted with the comment.
     *
     * @param $authorURL    string
     * @return $this
     */
    public function setCommentAuthorURL(string $authorURL) :self
    {
        $this->comment['comment_author_url'] = $authorURL;
        return $this;
    }

    /**
     * The comment's body text.
     *
     * @param $commentBody  string
     * @return $this
     */
    public function setCommentContent(string $commentBody) :self
    {
        $this->comment['comment_content'] = $commentBody;
        return $this;
    }

    /**
     * The UTC timestamp of the creation of the comment, in ISO 8607 format.
     * May be omitted if the comment is sent to the API at the time it is created
     *
     * @param $commentDateGmt string
     * @return $this
     */
    public function setCommentDateGmt(string $commentDateGmt) : self
    {
        $this->comment['comment_date_gmt'] = $commentDateGmt;
        return $this;
    }

    /**
     * The UTC timestamp of the publication time for the post, page,
     * thread on which the comment was posted
     *
     * @param $commentPostModifiedGmt string
     * @return Akismet
     */
    public function setCommentPostModifiedGmt(string $commentPostModifiedGmt) : self
    {
        $this->comment['comment_post_modified_gmt'] = $commentPostModifiedGmt;
        return $this;
    }

    /**
     * Indicates the language(s) in use on the blog or site, in ISO 369-1 format,
     * comma-separated. A site with articles in English and French might use "en,fr_CA"
     *
     * @param string $blogLang
     * @return Akismet
     */
    public function setBlogLang(string $blogLang) : self
    {
        $this->comment['blog_lang'] = $blogLang;
        return $this;
    }

    /**
     * The charset encoding for the form values included in `comment_*` parameters,
     * such as "UTF-8" or "ISO-8859-1".
     *
     * @param string $blogCharset
     * @return Akismet
     */
    public function setBlogCharset(string $blogCharset) : self
    {
        $this->comment['blog_charset'] = $blogCharset;
        return $this;
    }

    /**
     * The user role of the user who submitted the comment.
     * This is an optional parameter.
     * If you set it to 'administrator', Akismet will always return false
     *
     * @param string $userRole
     * @return Akismet
     */
    public function setUserRole(string $userRole) : self
    {
        $this->comment['user_role'] = $userRole;
        return $this;
    }

    /**
     * This is an optional parameter. You can use it when
     * submitted test queries to Akismet
     *
     * @param string $isTest
     * @return Akismet
     */
    public function setIsTest(string $isTest) : self
    {
        $this->comment['is_test'] = $isTest;
        return $this;
    }
}
