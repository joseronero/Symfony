<?php

namespace App\Util;

/**
 * Description of Calculadora
 *
 * @author Joseron
 */
class Calculadora {
    
   public function suma($a, $b){
        return $a + $b;
   }
   
   public function resta($a, $b){
        return $a - $b;
   }
   
   public function multiplicacion($a, $b){
        return $a * $b;
   }

   public function division($a, $b){
        return $a/$b;
   }
}
