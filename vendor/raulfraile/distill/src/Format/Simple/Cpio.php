<?php

/*
 * This file is part of the Distill package.
 *
 * (c) Raul Fraile <raulfraile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Distill\Format\Simple;

use Distill\Format\AbstractFormat;
use Distill\Format\FormatInterface;

class Cpio extends AbstractFormat
{
    /**
     * {@inheritdoc}
     */
    public function getCompressionRatioLevel()
    {
        return FormatInterface::RATIO_LEVEL_LOWEST;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return ['cpio'];
    }

    /**
     * {@inheritdoc}
     */
    public static function getClass()
    {
        return get_class();
    }
}