<?php

declare(strict_types=1);

namespace FOS\ChatBundle\Model;

enum SpamStatus: int
{
    case HAM = 1;
    case SPAM = 2;
    case DISCARD = 3;
}