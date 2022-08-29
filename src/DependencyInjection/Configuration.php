<?php

declare(strict_types=1);

/*
 * This file is part of Be Email.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/be_email
 */

namespace Markocupic\BeEmail\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('markocupic_be_email');

        $treeBuilder->getRootNode()
            ->children()
                ->integerNode('suggestions_list_max_length')->defaultValue(5)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
