<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 * @var Pollen\Social\Contracts\ChannelDriver $item
 */
?>
<?php if ($items = $this->get('items', [])) : ?>
<nav <?php echo $this->htmlAttrs($this->get('attrs', [])); ?>>
    <ul class="<?php echo $this->get('classes.items'); ?>">
        <?php foreach($items as $item) : ?>
        <li class="<?php echo $this->get('classes.item'); ?>">
            <?php echo $item->pageLink([
                'attrs' => [
                    'class' => $this->get('classes.link')
                ]
            ]); ?>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php endif;