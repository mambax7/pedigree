<?php

namespace XoopsModules\Pedigree;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */
\defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class Helper
 */
class Helper extends \Xmf\Module\Helper
{
    public $debug;

    /** @var self|null */
    protected static $helperInstance;

    /**
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        if (null === $this->dirname) {
            $dirname       = \basename(\dirname(__DIR__));
            $this->dirname = $dirname;
        }
        parent::__construct($this->dirname);
    }

    /**
     * @param bool $debug
     *
     * @return \XoopsModules\Pedigree\Helper
     */
    public static function getInstance($debug = false)
    {
        if (null === static::$helperInstance) {
            static::$helperInstance = new static($debug);
        }

        return static::$helperInstance;
    }

    /**
     * Replace the stored helper instance
     *
     * Primarily intended for unit tests where we want to inject
     * bespoke handler implementations without touching the
     * singleton bootstrap logic used in production.
     *
     * @param self|null $helper
     */
    public static function setInstance(?self $helper = null): void
    {
        static::$helperInstance = $helper;
    }

    /**
     * @return string
     */
    public function getDirname()
    {
        return $this->dirname;
    }

    /**
     * Get an Object Handler
     *
     * @param string $name name of handler to load
     *
     * @return bool|\XoopsObjectHandler|\XoopsPersistableObjectHandler
     */
    public function getHandler($name)
    {
        $ret   = false;
        $class = __NAMESPACE__ . '\\' . \ucfirst($name) . 'Handler';
        if (!\class_exists($class)) {
            throw new \RuntimeException("Class '$class' not found");
        }
        /** @var \XoopsMySQLDatabase $db */
        $db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $helper = self::getInstance();
        $ret    = new $class($db, $helper);
        $this->addLog("Getting handler '$name'");

        return $ret;
    }
}
