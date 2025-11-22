<?php

namespace FOS\ChatBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass(): string
    {
        return \FOS\ChatBundle\Tests\Functional\TestKernel::class;
    }
}
