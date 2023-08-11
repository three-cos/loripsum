<?php

namespace Wardenyarn\Loripsum;

trait WithLoremIpsum
{
    public function loremIpsum()
    {
        return new LoremIpsum();
    }
}
