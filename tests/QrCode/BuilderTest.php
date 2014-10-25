<?php namespace Arcanedev\QrCode\Tests;

use Arcanedev\QrCode\Builder                    as Builder;
use Arcanedev\QrCode\Contracts\BuilderInterface as BuilderInterface;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /* @var Builder */
    protected $builder;

    /* ------------------------------------------------------------------------------------------------
     |  Main Function
     | ------------------------------------------------------------------------------------------------
     */
    protected function setUp()
    {
        $this->builder = new Builder;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function testCanCreateBuilder()
    {
        $this->assertTrue(true);
    }
}
