<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Hierarchy\Loader;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class FileExtensionPredicate
{
    /**
     * @var string[]
     */
    private $extension = [];

    /**
     * @param string|string[] $extension
     */
    public function __construct($extension)
    {
        $extensions = is_string($extension) ? explode('|', $extension) : (array) $extension;
        foreach ($extensions as $extension) {
            is_string($extension) and $this->extension[] = strtolower(trim($extension, ". \t\n\r\0\x0B"));
        }
    }

    /**
     * @param  string $templatePath
     * @return bool
     */
    public function __invoke($templatePath)
    {
        $ext = strtolower(pathinfo($templatePath, PATHINFO_EXTENSION));

        return in_array($ext, $this->extension, true);
    }
}
