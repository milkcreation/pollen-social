<?php declare(strict_types=1);

namespace Pollen\Social\Channels;

use tiFy\Support\Proxy\Url;

class InstagramChannel extends SocialChannelDriver
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = 'instagram';

    /**
     * @inheritDoc
     */
    public function getDeeplink(): string
    {
        $uri = Url::set($this->get('uri'))->get();
        $id = ltrim(rtrim($uri->getPath(), '/'), '/');

        if ($this->isAndroidOS()) {
            return "intent://instagram.com/_u/{$id}/#Intent;package=com.instagram.android;scheme=https;end";
        } elseif ($this->isIOS()) {
            return "instagram://user?username={$id}";
        }

        return '';
    }
}
