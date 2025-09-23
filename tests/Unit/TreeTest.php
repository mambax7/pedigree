<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use XoopsModules\Pedigree\Helper;
use XoopsModules\Pedigree\Tree;

final class TreeTest extends TestCase
{
    /** @var StubTreeRepository */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = new StubTreeRepository();
        $helper = new class extends Helper {
            /** @var array<string,mixed> */
            private $handlers = [];

            public function setHandler(string $name, $handler): void
            {
                $this->handlers[strtolower($name)] = $handler;
            }

            public function getHandler($name)
            {
                $key = strtolower($name);
                if (isset($this->handlers[$key])) {
                    return $this->handlers[$key];
                }

                return parent::getHandler($name);
            }
        };
        $helper->setHandler('Tree', $this->repository);
        Helper::setInstance($helper);
    }

    protected function tearDown(): void
    {
        Helper::setInstance(null);
    }

    public function testGetParentsReturnsPopulatedParentInformation(): void
    {
        $mother = $this->repository->persistFromData(['id' => 101, 'pname' => 'Matriarch']);
        $father = $this->repository->persistFromData(['id' => 202, 'pname' => 'Patriarch']);

        $subject = new Tree();
        $subject->setVar('mother', $mother->getVar('id'));
        $subject->setVar('father', $father->getVar('id'));

        $parents = $subject->getParents();

        self::assertSame(['id' => 101, 'name' => 'Matriarch'], $parents['mother']);
        self::assertSame(['id' => 202, 'name' => 'Patriarch'], $parents['father']);
    }

    public function testGetParentsReturnsDefaultWhenParentMissing(): void
    {
        $subject = new Tree();
        $subject->setVar('mother', 0);
        $subject->setVar('father', 0);

        $parents = $subject->getParents();

        self::assertSame(['id' => 0, 'name' => ''], $parents['mother']);
        self::assertSame(['id' => 0, 'name' => ''], $parents['father']);
    }
}

final class StubTreeRepository
{
    /** @var array<int,Tree> */
    private $records = [];

    public function persistFromData(array $data): Tree
    {
        $tree = new Tree();
        $tree->assignVars($data);
        $tree->unsetNew();
        $this->records[(int)$tree->getVar('id')] = $tree;

        return $tree;
    }

    public function get(int $id): ?Tree
    {
        return $this->records[$id] ?? null;
    }
}
