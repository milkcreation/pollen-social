<?php declare(strict_types=1);

namespace Pollen\Social\Channels;

use BadMethodCallException;
use Exception;
use tiFy\View\Factory\PlatesFactory;

/**
 * @method string getIcon()
 * @method string getStatus()
 * @method bool getTitle()
 * @method bool isActive()
 */
class SocialChannelView extends PlatesFactory
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [
        'getIcon',
        'getStatus',
        'getTitle',
        'isActive'
    ];

    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) {
            try {
                return call_user_func_array([$this->engine->params('channel'), $name], $arguments);
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
