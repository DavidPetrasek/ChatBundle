<?php

namespace FOS\ChatBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    private static function getKernelClass()
    {
        return \FOS\ChatBundle\Tests\Functional\TestKernel::class;
    }
}
