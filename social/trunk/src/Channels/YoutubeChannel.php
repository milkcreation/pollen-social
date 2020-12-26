<?php declare(strict_types=1);

namespace Pollen\Social\Channels;

use tiFy\Support\Proxy\Url;

class YoutubeChannel extends SocialChannelDriver
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = 'youtube';

    /**
     * @inheritDoc
     */
    public function getDeeplink(): string
    {
        $uri = Url::set($this->get('uri'))->get();
        $id = $uri->getPath();

        if ($this->isAndroidOS()) {
            return "intent://www.youtube.com{$id}#Intent;package=com.google.android.youtube;scheme=https;end";
        } elseif ($this->isIOS()) {
            return "vnd.youtube://www.youtube.com{$id}";
        }

        return '';
    }
}
