<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Payment\Object\Fields;

use Payment\Enum\AbstractEnum;

abstract class PayMethod extends AbstractEnum {

    const APPLE = "apple";
    const WINDOW_PHONE = "wp";
    const GOOGLE = "google";
    
    const TYPE = "inapp";

}
