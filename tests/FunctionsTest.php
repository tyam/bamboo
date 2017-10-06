<?php

namespace tyam\bamboo\tests;

use \PHPUnit\Framework\TestCase;
use tyam\bamboo\Engine;

class FunctionsTest extends TestCase
{
    function testFunctions() {
        Engine::loadFunctions();
        $this->assertTrue(function_exists('eh'));
    }
}