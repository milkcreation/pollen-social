<?php

declare(strict_types=1);

namespace Pollen\Social\Adapters;

use Pollen\Social\Channels\SocialChannelDriverInterface;
use Pollen\Social\Contracts\SocialContract;

class WordpressAdapter extends AbstractSocialAdapter
{
    /**
     * @param SocialContract $socialManager
     */
    public function __construct(SocialContract $socialManager)
    {
        parent::__construct($socialManager);

        events()->listen(
            'social.booting',
            function () {
                register_setting('tify_options', 'tify_social_share');
            }
        );

        events()->listen(
            'social.booted',
            function () {
                if ($this->socialManager()->config('admin', true)) {
                    $this->socialManager()->metaboxManager()->add(
                        md5('social'),
                        [
                            'title' => __('Réseaux sociaux', 'tify'),
                        ],
                        'tify_options@options',
                        'tab'
                    );
                }
            }
        );

        events()->listen(
            'social.channel.booting',
            function (string $name, SocialChannelDriverInterface $channel) {
                if ($params = get_option('tify_social_share')) {
                    $channel->params($params[$channel->getOptionNameKey()] ?? []);
                }
            }
        );

        events()->listen(
            'social.channel.booted',
            function (string $name, SocialChannelDriverInterface $channel) {
                if ($channel->hasAdmin()) {
                    $this->socialManager()->metaboxManager()->add(
                        "social-{$name}",
                        [
                            'driver' => "social.channel.{$name}",
                            'parent' => 'social',
                        ],
                        'tify_options@options',
                        'tab'
                    );
                }
            }
        );
    }
}
