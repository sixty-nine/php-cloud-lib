<?php

namespace SixtyNine\Cloud\Color;

interface ColorGeneratorInterface
{
    /** @return string */
    function getNextColor();
}