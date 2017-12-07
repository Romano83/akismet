# Akismet PHP 7 library
==

A PSR-7 library to communicate with [Akismet](kttps://akismet.com) service to determine if a submitted comment to your website should be considered spam or not.

## Installation
--

### Requirements
* PHP 7.1

### Steps to install

Run : `composer require romano83/akismet` or in your composer.json file

```
"require": {
    "romano83/akismet" : "^1.0"
}
```

## How to use it
--
Before to use it, you need an [Akismet API key](https://akismet.com/). 
Once you have one, in order to check if a comment is a spam:

```
<?php
use Romano83\Akismet;

$website = 'https://your-website.com'; // must be a full URI including http:// or https://
$apikey = 'YOUR_API_KEY';
$akismet = new Akismet($website, $apikey);

$akismet->setCommentAuthor($author)
    ->setCommentAuthorEmail($email)
    ->setCommentContent($comment);
    
if ($akismet->isCommentSpam()) {
    // store the comment and mark it as a spam
} else {
    // store the comment normally
}
``` 
It's that simple!

If Akismet filter wrongly tags messages, you can use this following methods : 

```
$akismet->submitSpam();
```
or
```
$akismet->submitHam();
```
to submit mis-diagnosed spam and ham, which improves the system for everybody.

### Others methods
This class provides you a set of methods in order to add parameters for comment check or submitted spam and ham.
This methods are : 
* setUserIp (required)
* setUserAgent (required)
* setReferrer (note spelling)
* setPermalink
* setCommentType
* setCommentAuthor
* setCommentAuthorEmail
* setCommentAuthorUrl
* setCommentContent
* setCommentDateGmt
* setCommentPostModifiedGmt
* setBlogLang
* setBlogCharset
* setUserRole
* setIsTest

All methods return self in order to have a fluent interface.

If you want more details for each method, look at the internal documentation or follow this [link](https://akismet.com/development/api/#comment-check).


## How to contribute
-- 
* create a ticket in Github if you have found a bug.
* create a new branch if you want to do a PR.
* you **must** add testcases if needed