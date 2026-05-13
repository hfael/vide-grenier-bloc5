<?php

class TestRunner
{
    private $passed = 0;
    private $failed = 0;

    public function test($name, callable $callback)
    {
        try {
            $callback($this);
            $this->passed++;
            echo "[OK] " . $name . PHP_EOL;
        } catch (Throwable $exception) {
            $this->failed++;
            echo "[FAIL] " . $name . PHP_EOL;
            echo "       " . $exception->getMessage() . PHP_EOL;
        }
    }

    public function assertTrue($condition, $message = 'Expected condition to be true')
    {
        if (!$condition) {
            throw new Exception($message);
        }
    }

    public function assertSame($expected, $actual, $message = 'Values are not identical')
    {
        if ($expected !== $actual) {
            throw new Exception($message . ' | expected=' . var_export($expected, true) . ' actual=' . var_export($actual, true));
        }
    }

    public function assertContains($needle, $haystack, $message = 'Expected text was not found')
    {
        if (strpos($haystack, $needle) === false) {
            throw new Exception($message . ' | missing=' . $needle);
        }
    }

    public function assertNotContains($needle, $haystack, $message = 'Unexpected text was found')
    {
        if (strpos($haystack, $needle) !== false) {
            throw new Exception($message . ' | found=' . $needle);
        }
    }

    public function expectException(callable $callback, $message = 'Expected exception was not thrown')
    {
        try {
            $callback();
        } catch (Throwable $exception) {
            return;
        }

        throw new Exception($message);
    }

    public function finish()
    {
        echo PHP_EOL . "Tests passed: {$this->passed}" . PHP_EOL;
        echo "Tests failed: {$this->failed}" . PHP_EOL;

        if ($this->failed > 0) {
            exit(1);
        }
    }
}
