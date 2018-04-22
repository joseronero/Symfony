<?php
/**
 * Description of CalculadoraTest
 *
 * @author Joseron
 */
namespace App\Tests\Util;

use App\Util\Calculadora;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{   
    public function testSuma(){
        $calculadora = new Calculadora();
        $resultado = $calculadora->suma(30, 12);
        $this->assertEquals(42, $resultado);
    }

    public function testResta(){
        $calculadora = new Calculadora();
        $resultado = $calculadora->resta(30, 12);
        $this->assertEquals(18, $resultado);
    }

    public function testMultiplicacion(){
        $calculadora = new Calculadora();
        $resultado = $calculadora->multiplicacion(5, 12);
        $this->assertEquals(60, $resultado);
    }

    public function testDivision(){
        $calculadora = new Calculadora();
        $resultado = $calculadora->division(10, 1);
        $this->assertEquals(10, $resultado);
    }
}
 
