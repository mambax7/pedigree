<?php

declare(strict_types=1);

namespace {

// Define XOOPS constants expected by the module configuration files
if (!\defined('XOOPS_ROOT_PATH')) {
    \define('XOOPS_ROOT_PATH', \dirname(__DIR__));
}
if (!\defined('XOOPS_URL')) {
    \define('XOOPS_URL', 'https://xoops.invalid');
}
if (!\defined('XOOPS_UPLOAD_PATH')) {
    \define('XOOPS_UPLOAD_PATH', XOOPS_ROOT_PATH . '/uploads');
}
if (!\defined('XOOPS_UPLOAD_URL')) {
    \define('XOOPS_UPLOAD_URL', XOOPS_URL . '/uploads');
}
if (!\defined('_EDIT')) {
    \define('_EDIT', 'Edit');
}
if (!\defined('_DELETE')) {
    \define('_DELETE', 'Delete');
}
if (!\defined('_CLONE')) {
    \define('_CLONE', 'Clone');
}
if (!\defined('_PREVIEW')) {
    \define('_PREVIEW', 'Preview');
}
if (!\defined('_PRINT')) {
    \define('_PRINT', 'Print');
}
if (!\defined('_PDF')) {
    \define('_PDF', 'Pdf');
}
if (!\defined('_ADD')) {
    \define('_ADD', 'Add');
}
if (!\defined('_OFF')) {
    \define('_OFF', 'Off');
}
if (!\defined('_ON')) {
    \define('_ON', 'On');
}
if (!\defined('XOBJ_DTYPE_INT')) {
    \define('XOBJ_DTYPE_INT', 1);
}
if (!\defined('XOBJ_DTYPE_TXTAREA')) {
    \define('XOBJ_DTYPE_TXTAREA', 2);
}
if (!\defined('XOBJ_DTYPE_TXTBOX')) {
    \define('XOBJ_DTYPE_TXTBOX', 3);
}
if (!\defined('XOBJ_DTYPE_ENUM')) {
    \define('XOBJ_DTYPE_ENUM', 4);
}

// Provide a minimal $xoops helper with the path() method expected by the module
if (!isset($GLOBALS['xoops'])) {
    $GLOBALS['xoops'] = new class {
        public function path(string $path): string
        {
            return XOOPS_ROOT_PATH . '/' . \ltrim($path, '/');
        }
    };
}

// Lightweight stubs that mimic the pieces of XOOPS used by the tests
if (!\class_exists('XoopsObject')) {
    abstract class XoopsObject
    {
        /** @var array<string,mixed> */
        protected $vars = [];
        /** @var bool */
        protected $isNew = true;

        public function __construct()
        {
        }

        public function initVar($key, $type, $default = null, $required = false, $maxlength = null): void
        {
            $this->vars[$key] = $default;
        }

        public function setVar(string $key, $value): void
        {
            $this->vars[$key] = $value;
        }

        public function assignVars(array $values): void
        {
            foreach ($values as $key => $value) {
                $this->setVar($key, $value);
            }
            $this->unsetNew();
        }

        public function getVar(string $key)
        {
            return $this->vars[$key] ?? null;
        }

        public function setNew(): void
        {
            $this->isNew = true;
        }

        public function unsetNew(): void
        {
            $this->isNew = false;
        }

        public function isNew(): bool
        {
            return $this->isNew;
        }
    }
}

if (!\class_exists('XoopsPersistableObjectHandler')) {
    abstract class XoopsPersistableObjectHandler
    {
        /** @var array<int|string,XoopsObject> */
        protected $objects = [];
        /** @var string */
        protected $className;
        /** @var string */
        protected $keyName;

        public function __construct($db = null, string $table = '', string $className = '', string $keyName = '', string $identifierName = '')
        {
            $this->className = $className;
            $this->keyName   = $keyName;
        }

        public function create(bool $isNew = true): XoopsObject
        {
            $class = $this->className;
            /** @var XoopsObject $object */
            $object = new $class();
            if ($isNew) {
                $object->setNew();
            } else {
                $object->unsetNew();
            }

            return $object;
        }

        public function insert(XoopsObject $object)
        {
            $key = $object->getVar($this->keyName);
            $this->objects[$key] = $object;

            return $key;
        }

        public function get($id)
        {
            return $this->objects[$id] ?? null;
        }
    }
}

if (!\class_exists('XoopsDatabaseFactory')) {
    class XoopsDatabaseFactory
    {
        public static function getDatabaseConnection()
        {
            return new XoopsMySQLDatabase();
        }
    }
}

if (!\class_exists('XoopsDatabase')) {
    class XoopsDatabase
    {
    }
}

if (!\class_exists('XoopsMySQLDatabase')) {
    class XoopsMySQLDatabase extends XoopsDatabase
    {
    }
}

if (!\class_exists('Criteria')) {
    class Criteria
    {
        public function __construct($column = null, $value = null, $operator = '=', $prefix = '', $function = '')
        {
        }
    }
}

if (!\class_exists('CriteriaCompo')) {
    class CriteriaCompo extends Criteria
    {
        public function add($criteria, $condition = 'AND')
        {
            return $this;
        }

        public function setSort($sort)
        {
            return $this;
        }

        public function setOrder($order)
        {
            return $this;
        }

        public function setLimit($limit)
        {
            return $this;
        }
    }
}

if (!\class_exists('Xmf\\Module\\Helper')) {
    \class_alias('PedigreeTest\\XmfModuleHelperStub', 'Xmf\\Module\\Helper');
}

}

namespace PedigreeTest {
    class XmfModuleHelperStub
    {
        protected $dirname;

        public function __construct(string $dirname)
        {
            $this->dirname = $dirname;
        }

        public function addLog(string $message): void
        {
        }
    }
}

namespace {
    if (!\class_exists('Xmf\\Module\\Admin')) {
        class_alias('PedigreeTest\\XmfModuleAdminStub', 'Xmf\\Module\\Admin');
    }
}

namespace PedigreeTest {
    class XmfModuleAdminStub
    {
        public static function iconUrl(string $path = '', int $size = 16): string
        {
            $path = \trim($path, '/');
            if ('' === $path) {
                return 'icons';
            }

            return 'icons/' . $path;
        }
    }
}

namespace {
    if (!\class_exists('PHPUnit\\Framework\\TestCase')) {
        require __DIR__ . '/support/TestCase.php';
    }

    spl_autoload_register(static function (string $class): void {
        $prefix = 'XoopsModules\\Pedigree\\';
        if (0 === strpos($class, $prefix)) {
            $relative = substr($class, \strlen($prefix));
            $relativePath = str_replace('\\', '/', $relative);
            $paths = [
                __DIR__ . '/../class/' . $relativePath . '.php',
                __DIR__ . '/../preloads/' . $relativePath . '.php',
            ];
            foreach ($paths as $file) {
                if (is_file($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    });
}
