<?php
/**
 * @var Pollen\Social\Metabox\ChannelMetaboxView $this
 * @var Pollen\Social\Contracts\ChannelDriver $channel
 */
?>
<table class="Form-table">
    <tr>
        <th>
            <?php _e('Activation de la prise en charge du réseau.', 'tify'); ?><br>
        </th>
        <td>
            <?php echo field('toggle-switch', [
                'name'  => $this->name() . '[active]',
                'value' => filter_var($this->value('active'), FILTER_VALIDATE_BOOLEAN) ? 'on' : 'off',
            ]); ?>
        </td>
    </tr>
</table>
<table class="Form-table">
    <tr>
        <th>
            <?php _e('Url vers la page du compte.', 'tify'); ?><br>
        </th>
        <td>
            <?php echo field('text', [
                'name'  => $this->name() . '[uri]',
                'value' => $this->value('uri'),
                'attrs' => [
                    'size'        => 80,
                    'placeholder' => __('https://[url-du-service]/[nom-de-la-page]', 'tify'),
                ],
            ]); ?>
            <em style="display:block;font-size:0.8em;color:#AAA;">
                <?php _e('L\'url doit être renseignée pour que le lien s\'affiche sur votre site.', 'tify'); ?>
            </em>
        </td>
    </tr>
</table>
<?php if ($this->isSharer()) : ?>
<table class="Form-table">
    <tr>
        <th>
            <?php _e('Permettre le partage de publication du site sur ce réseau.', 'tify'); ?><br>
        </th>
        <td>
            <?php echo field('toggle-switch', [
                'name'  => $this->name() . '[share]',
                'value' => filter_var($this->value('share'), FILTER_VALIDATE_BOOLEAN) ? 'on' : 'off',
            ]); ?>
        </td>
    </tr>
</table>
<?php endif; ?>
<table class="Form-table">
    <tr>
        <th>
            <?php _e('Ordre d\'affichage.', 'tify'); ?><br>
        </th>
        <td>
            <?php echo field('number', [
                'name'  => $this->name() . '[order]',
                'value' => $this->value('order'),
                'attrs' => [
                    'size' => 2,
                ],
            ]); ?>
        </td>
    </tr>
</table>