<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 * @var Pollen\Social\Contracts\ChannelDriver $item
 * @var tiFy\Wordpress\Contracts\Query\QueryPost|null $post
 */
?>
<?php if ($items = $this->get('items', [])) : ?>
    <nav <?php echo $this->htmlAttrs($this->get('attrs', [])); ?>>
        <ul class="SocialShare-channels">
            <?php foreach ($items as $item) : ?>
                <li class="SocialShare-channel SocialShare-channel--<?php echo $item->getName(); ?>">
                    <?php $this->insert('item', compact('item', 'post')); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
<?php endif;