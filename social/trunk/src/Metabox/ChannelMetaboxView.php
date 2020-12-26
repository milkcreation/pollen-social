<?php declare(strict_types=1);

namespace Pollen\Social\Metabox;

use BadMethodCallException;
use Exception;
use tiFy\Metabox\MetaboxView;

/**
 * @method string getIcon()
 * @method string getStatus()
 * @method bool getTitle()
 * @method bool isActive()
 * @method bool isSharer()
 */
class ChannelMetaboxView extends MetaboxView
{
    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        $channelMixins = [
            'getIcon',
            'getStatus',
            'getTitle',
            'isActive',
            'isSharer'
        ];

        if (in_array($name, $channelMixins)) {
            try {
                $channel = $this->engine->params('channel');

                return $channel->{$name}(...$arguments);
            } catch (Exception $e) {
                throw new BadMethodCallException(sprintf(
                    __CLASS__ . ' throws an exception during the method call [%s] with message : %s',
                    $name, $e->getMessage()
                ));
            }
        } else {
            return parent::__call($name, $arguments);
        }
    }
}
