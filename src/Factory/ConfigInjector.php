<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-tooling for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-tooling/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Tooling\Factory;

use const SORT_NATURAL;

/**
 * Inject factory configuration in an autoloadable location.
 *
 * This class will re-generate the file denoted by its CONFIG_FILE constant.
 * It first pulls in the data in that file, if the file exists, and then adds
 * an entry for the given class, pointing it to the given factory, rewriting
 * the configuration file on completion.
 */
class ConfigInjector
{
    public const CONFIG_FILE = 'config/autoload/zend-expressive-tooling-factories.global.php';

    public const TEMPLATE = <<<'EOT'
<?php
/**
 * This file generated by %1$s.
 *
 * Modifications should be kept at a minimum, and restricted to adding or
 * removing factory definitions; other dependency types may be overwritten
 * when regenerating this file via zend-expressive-tooling commands.
 */

return [
    'dependencies' => [
        'factories' => [
%2$s,
        ],
    ],
];

EOT;

    /**
     * @var string
     */
    private $configFile;

    public function __construct(string $projectRoot = '')
    {
        $this->configFile = $projectRoot === ''
            ? self::CONFIG_FILE
            : sprintf('%s/%s', rtrim($projectRoot, '/'), self::CONFIG_FILE);
    }

    public function injectFactoryForClass(string $factory, string $class) : string
    {
        if (! $this->configIsWritable()) {
            throw ConfigFileNotWritableException::forFile($this->configFile);
        }

        $config = file_exists($this->configFile) ? include $this->configFile : [];
        $config = $config['dependencies']['factories'] ?? [];
        $config[$class] = $factory;
        $configContents = sprintf(
            self::TEMPLATE,
            __CLASS__,
            $this->normalizeConfig($config)
        );
        file_put_contents($this->configFile, $configContents);

        return $this->configFile;
    }

    private function configIsWritable() : bool
    {
        return is_writable($this->configFile)
            || (! file_exists($this->configFile) && is_writable(dirname($this->configFile)));
    }

    private function normalizeConfig(array $config) : string
    {
        $normalized = [];
        ksort($config, SORT_NATURAL);
        foreach ($config as $class => $factory) {
            $class .= '::class';
            $factory .= '::class';

            $normalized[] = sprintf('%s%s => %s', str_repeat(' ', 12), $class, $factory);
        }
        return implode(",\n", $normalized);
    }
}
