<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Hierarchy\Tests\Unit\Loader;

use Brain\Hierarchy\Loader\FileExtensionPredicate;
use Brain\Hierarchy\Tests\TestCase;


/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class FileExtensionPredicateTest extends TestCase
{

    public function testSingleExtension()
    {
        /** @var callable $predicate */
        $predicate = new FileExtensionPredicate('php');

        assertTrue($predicate('index.php'));
        assertTrue($predicate('foo.PHP'));
        assertFalse($predicate('foo.phtml'));
        assertFalse($predicate('.phtml'));
    }

    public function testSingleExtensionNormalize()
    {
        /** @var callable $predicate */
        $predicate = new FileExtensionPredicate(' .pHP ');

        assertTrue($predicate('foo.php'));
        assertTrue($predicate('foo.PHP'));
        assertFalse($predicate('foo.phtml'));
    }

    public function testMultiExtensionString()
    {
        /** @var callable $predicate */
        $predicate = new FileExtensionPredicate(' php | PHTML | .inc ');

        assertTrue($predicate('foo.php'));
        assertTrue($predicate('foo.PHP'));
        assertTrue($predicate('foo.phtml'));
        assertTrue($predicate('foo.inc'));
        assertFalse($predicate('foo.twig'));
    }

    public function testMultiExtensionArray()
    {
        /** @var callable $predicate */
        $predicate = new FileExtensionPredicate([' php ', 'PHTML ', ' .inc']);

        assertTrue($predicate('foo.php'));
        assertTrue($predicate('foo.PHP'));
        assertTrue($predicate('foo.phtml'));
        assertTrue($predicate('foo.inc'));
        assertFalse($predicate('foo.twig'));
    }
}