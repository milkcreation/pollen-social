<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 * @var Pollen\Social\Contracts\ChannelDriver $item
 * @var tiFy\Wordpress\Contracts\Query\QueryPost|null $post
 */
echo partial('tag', [
    'attrs'   => [
        'class'        => 'SocialShare-channelLink SocialShare-channelLink--' . $item->getName(),
        'data-control' => 'social.share',
        'href'         => $item->getShareUrl($this->get("channel.{$item->getName()}", [])),
        'rel'          => 'nofollow noopener noreferrer',
        'target'       => '_blank',
    ],
    'content' => $item->getIcon(),
    'tag'     => 'a',
]);