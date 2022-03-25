<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * Test formatOrderStatus function
     *
     * @return void
     */
    public function testStatusShouldBeFormattedCorrectly()
    {
        $this->assertEquals(1, formatOrderStatus(1) );
        $this->assertEquals(1, formatOrderStatus(2) );
        $this->assertEquals(2, formatOrderStatus(3) );
        $this->assertEquals(2, formatOrderStatus(4) );
        $this->assertEquals(3, formatOrderStatus(6) );
        $this->assertEquals(3, formatOrderStatus(7) );
       
        $this->expectException(\Exception::class);

        formatOrderStatus(500);
    }

    /**
     * Test getStatusDescription function
     *
     * @return void
     */
    public function testShouldGetStatusDescriptionCorrectly()
    {
        $this->assertEquals('Aguardando Pgto', getStatusDescription(1) );
        $this->assertEquals('Pago', getStatusDescription(2) );
        $this->assertEquals('Cancelado', getStatusDescription(3) );
        $this->assertEquals('', getStatusDescription(50) );
    }

}
