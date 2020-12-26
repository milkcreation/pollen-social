<?php declare(strict_types=1);

namespace Pollen\Social\Channels;

class FacebookChannel extends SocialChannelDriver
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = 'facebook';

    /**
     * Url de partage et indicateur de partage possible sur le réseau.
     * @var string
     */
    protected $sharer = 'https://www.facebook.com/sharer.php';

    /**
     * @inheritDoc
     */
    public function getDeeplink(): string
    {
        if ($id = $this->get('profile_id')) {
            if ($this->isAndroidOS()) {
                return "intent://page/{$id}?referrer=app_link#Intent;package=com.facebook.katana;scheme=fb;end";
            } elseif ($this->isIOS()) {
                return "fb://page/?id={$id}";
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function setPostShare($post): SocialChannelDriverInterface
    {
        $this->share_params = [
            'u' => $post->getPermalink()
        ];

        return $this;
    }
}
