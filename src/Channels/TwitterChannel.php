<?php declare(strict_types=1);

namespace Pollen\Social\Channels;

use tiFy\Support\Proxy\Url;

class TwitterChannel extends SocialChannelDriver
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = 'twitter';

    /**
     * Url de partage et indicateur de partage possible sur le réseau.
     * @see https://developer.twitter.com/en/docs/twitter-for-websites/tweet-button/guides/web-intent
     * @var string
     */
    protected $sharer = 'https://twitter.com/intent/tweet';

    /**
     * @inheritDoc
     */
    public function getDeeplink(): string
    {
        $uri = Url::set($this->get('uri'))->get();
        $id = ltrim(rtrim($uri->getPath(), '/'), '/');

        if ($this->isAndroidOS()) {
            return "intent://twitter.com/{$id}#Intent;package=com.twitter.android;scheme=https;end";
        } elseif ($this->isIOS()) {
            return "twitter://user?screen_name={$id}";
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function setPostShare($post): SocialChannelDriverInterface
    {
        $this->share_params = [
            'text' => str_replace('|', '', strip_tags($post->getTitle())),
            'url'  => $post->getPermalink(),
        ];

        return $this;
    }
}
