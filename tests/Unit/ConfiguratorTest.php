<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use XoopsModules\Pedigree\Common\Configurator;

final class ConfiguratorTest extends TestCase
{
    public function testConfiguratorBootstrapsModulePathsAndIcons(): void
    {
        $configurator = new Configurator();

        self::assertSame('PEDIGREE ModuleConfigurator', $configurator->name);
        self::assertNotNull($configurator->paths, 'Paths configuration should be initialised.');
        self::assertNotNull($configurator->icons, 'Icon configuration should be initialised.');

        $uploadFolders = $configurator->uploadFolders;
        self::assertTrue(in_array(XOOPS_UPLOAD_PATH . '/pedigree/pedigree_config', $uploadFolders, true));

        $paths = (array)$configurator->paths->paths;
        self::assertSame(XOOPS_ROOT_PATH . '/modules/pedigree', $paths['modPath']);
        self::assertSame(XOOPS_URL . '/modules/pedigree', $paths['modUrl']);

        $icons = (array)$configurator->icons;
        self::assertArrayHasKey('edit', $icons);
        self::assertArrayHasKey('delete', $icons);
        self::assertArrayHasKey('add', $icons);
    }
}
