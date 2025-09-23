<?php
declare(strict_types=1);

namespace PHPUnit\Framework;

use Throwable;

class AssertionFailedError extends \Exception
{
}

abstract class TestCase
{
    /** @var list<array{test:string,status:string,message?:string,exception?:Throwable}> */
    private $results = [];

    /**
     * Override in child classes to provide per-test setup
     */
    protected function setUp(): void
    {
    }

    /**
     * Override in child classes to provide per-test teardown
     */
    protected function tearDown(): void
    {
    }

    /**
     * Execute all test* methods in the current test case.
     *
     * @return array{class:string,count:int,passed:int,failures:list<mixed>,errors:list<mixed>,details:list<mixed>}
     */
    public function run(): array
    {
        $methods = array_filter(get_class_methods($this), static function (string $method): bool {
            return 0 === strpos($method, 'test');
        });
        sort($methods);

        $failures = [];
        $errors   = [];
        $details  = [];

        foreach ($methods as $method) {
            $status   = 'passed';
            $message  = '';
            $exception = null;

            try {
                $this->setUp();
                $this->$method();
            } catch (AssertionFailedError $failure) {
                $status   = 'failure';
                $message  = $failure->getMessage();
                $exception = $failure;
            } catch (Throwable $throwable) {
                $status   = 'error';
                $message  = $throwable->getMessage();
                $exception = $throwable;
            }

            try {
                $this->tearDown();
            } catch (Throwable $teardown) {
                $status   = 'error';
                $message  = 'tearDown failure: ' . $teardown->getMessage();
                $exception = $teardown;
            }

            if ('failure' === $status) {
                $failures[] = ['test' => $method, 'message' => $message, 'exception' => $exception];
            } elseif ('error' === $status) {
                $errors[] = ['test' => $method, 'message' => $message, 'exception' => $exception];
            }

            $details[] = ['test' => $method, 'status' => $status, 'message' => $message];
        }

        $count  = count($methods);
        $passed = $count - count($failures) - count($errors);

        return [
            'class'    => static::class,
            'count'    => $count,
            'passed'   => $passed,
            'failures' => $failures,
            'errors'   => $errors,
            'details'  => $details,
        ];
    }

    protected static function failureDescription($expected, $actual): string
    {
        return 'Failed asserting that ' . var_export($actual, true) . ' matches expected ' . var_export($expected, true);
    }

    public static function assertTrue($condition, string $message = ''): void
    {
        if (true !== $condition) {
            throw new AssertionFailedError($message ?: 'Failed asserting that condition is true.');
        }
    }

    public static function assertFalse($condition, string $message = ''): void
    {
        if (false !== $condition) {
            throw new AssertionFailedError($message ?: 'Failed asserting that condition is false.');
        }
    }

    public static function assertSame($expected, $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            throw new AssertionFailedError($message ?: self::failureDescription($expected, $actual));
        }
    }

    public static function assertEquals($expected, $actual, string $message = ''): void
    {
        if ($expected != $actual) {
            throw new AssertionFailedError($message ?: self::failureDescription($expected, $actual));
        }
    }

    public static function assertInstanceOf(string $expected, $actual, string $message = ''): void
    {
        if (!($actual instanceof $expected)) {
            throw new AssertionFailedError($message ?: 'Failed asserting that object is instance of ' . $expected . '.');
        }
    }

    public static function assertNull($actual, string $message = ''): void
    {
        if (null !== $actual) {
            throw new AssertionFailedError($message ?: 'Failed asserting that value is null.');
        }
    }

    public static function assertNotNull($actual, string $message = ''): void
    {
        if (null === $actual) {
            throw new AssertionFailedError($message ?: 'Failed asserting that value is not null.');
        }
    }

    public static function assertArrayHasKey($key, array $array, string $message = ''): void
    {
        if (!array_key_exists($key, $array)) {
            throw new AssertionFailedError($message ?: sprintf('Failed asserting that the array has the key %s.', var_export($key, true)));
        }
    }

    public static function fail(string $message = ''): void
    {
        throw new AssertionFailedError($message ?: 'Failed assertion.');
    }
}
