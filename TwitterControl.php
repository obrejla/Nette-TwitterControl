<?php

/**
 * TwitterControl - plugin for Nette Framework for using Twitter.
 *
 * @copyright  Copyright (c) 2009 Ondřej Brejla
 * @license    New BSD License
 * @link       http://github.com/OndrejBrejla/Nette-TwitterControl
 * @package    Nette\Extras
 * @version    0.1
 */
class TwitterControl extends Control {

    /**
     * Twitter username.
     *
     * @var string
     */
    private $username;

    /**
     * Twitter password.
     *
     * @var string
     */
    private $password;

    /**
     * Number of shown tweets.
     *
     * @var int
     */
    private $numberOfTweets = 1;

    /**
     * Helper for converting @nick and #search.
     *
     * @param string $text
     * @return string
     */
    public static function twitterLinks($text) {
        $search = array('|(http://[^ ]+)|', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(|\s)#(\w+)/i');
        $replace = array('<a href="$1" title="$1">$1</a>', '$1<a href="http://twitter.com/$2" title="$2">@$2</a>', '$1<a href="http://twitter.com/search?q=%23$2" title="$2">#$2</a>');
        $text = preg_replace($search, $replace, $text);

        return $text;
    }

    /**
     * Helper for viewing date in required format.
     *
     * @param string $date
     * @return string
     */
    public static function twitterDate($date) {
        return date('j.n.Y H:i', strtotime($date));
    }

    /**
     * Creates new TwitterControl object and sets username and password.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Sets the number of shown tweets.
     *
     * @param int $numberOfTweets
     */
    public function setNumberOfTweets($numberOfTweets) {
        $this->numberOfTweets = (int) $numberOfTweets;
    }

    /**
     * Returns the number of shown tweets.
     *
     * @return int
     */
    public function getNumberOfTweets() {
        return $this->numberOfTweets;
    }

    /**
     * Loads tweets from twitter account.
     *
     * @return SimpleXMLIterator
     */
    public function load() {
        $twitterUrl = 'http://twitter.com/statuses/user_timeline/' . $this->username . '.xml?count=' . $this->numberOfTweets;
        
        $buffer = file_get_contents($twitterUrl);
        if ($buffer === FALSE) {
            return array();
        }

        return new SimpleXMLIterator($buffer);
    }

    /**
     * Sends new tweet to twitter account.
     *
     * @param string $text
     * @throws NotImplementedException
     */
    public function send($text) {
        throw new NotImplementedException();
    }

    /**
     * Renders Twitter control into template.
     */
    public function render() {
        $this->template->setFile(dirname(__FILE__) . '/TwitterControl.phtml');
        $this->template->registerHelper('twitterLinks', 'TwitterControl::twitterLinks');
        $this->template->registerHelper('twitterDate', 'TwitterControl::twitterDate');

        $this->template->statuses = $this->load();

        $this->template->render();
    }

}
