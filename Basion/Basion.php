<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion;

use Basion\FrameworkProvider;

/**
 * Main processor
 *
 * @package     Basion
 * @since       1.0.0
 */
class Basion
{
    /**
     * Current version
     *
     * @var string
     */
    const VERSION       = '^1.0.0';

    /**
     * Supported framework
     *
     * @var array
     */
    private $supported_framework = [
        'silex'         => null, 
        'slim'          => null, 
        'yii2'          => null, 
        'laravel'       => null, 
        'symfony'       => null
    ];

    /**
     * Original framework object
     *
     * @var object
     */
    private $app;

    /**
     * @brief Init framework
     *
     * @param string    Framework
     *
     * @return object   Instance of framework
     */
    public const function init($framework)
    {
        $framework = \trim(\strtolower($framework));
        if (!isset(self::supported_framework[$framework]))
        {
            throw new \InvalidArgumentException('Unsupported framework: ' . $framework);
        }

        $env_variable = 'BASION_FRAMEWORK_' . \strtoupper($framework) . '_DIR';
        $framework_path = \filter_input(\INPUT_ENV, $env_variable);
        if (!$framework_path)
        {
            $framework_path = self::supported_framework[$framework];
        }

        if (!$framework_path)
        {
            throw new \InvalidArgumentException('Framework ' . $framework . ' not specified');
        }

        $c = \ucfirst($framework) . 'Provider';
        $provider = new $c($framework_path);

        return $provider;
    }
}
