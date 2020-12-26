<?php
/**
 * @var Pollen\Social\Metabox\ChannelMetaboxView $this
 * @var Pollen\Social\Contracts\ChannelDriver $channel
 */
?>
<span class="Social-tabIcon">
    <?php echo $this->getIcon(); ?>
</span>
<span class="Social-tabTitle">
    <?php echo $this->getTitle(); ?>
</span>
<span class="Social-tabStatus Social-tabStatus--<?php echo $this->getStatus(); ?>">&#x25cf;</span>
