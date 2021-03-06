<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-tooling for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-tooling/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

use Zend\Expressive\Template\TemplateRendererInterface;

return [
    'templates' => [
        'extension' => '%s',
        'paths' => [
            'test' => ['templates/test'],
        ],
    ],
];
