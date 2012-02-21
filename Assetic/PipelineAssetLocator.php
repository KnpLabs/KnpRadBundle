<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\Assetic;

use Symfony\Component\Finder\Finder;

/**
 * Locates/parses pipeline assets.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PipelineAssetLocator
{
    private $paths = array();
    private $filters = array();

    /**
     * Initializes locator.
     *
     * @param array $paths   List of paths where to search
     * @param array $filters List of filter names, that could be used
     */
    public function __construct(array $paths = array(), array $filters = array())
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
        $this->filters = $filters;
    }

    /**
     * Adds path to locator.
     *
     * @param string $path Pipeline path
     */
    public function addPath($path)
    {
        $this->paths[] = rtrim($path, '/');
    }

    /**
     * Returns list of pipeline paths.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Locate assets by pipeline alias (relative path).
     *
     * @param string $input Pipeline alias
     * @param string $type  Asset type (js, css)
     *
     * @return array        Array of asset formulas
     */
    public function locatePipelinedAssets($input, $type = null)
    {
        if (null === $type) {
            $type = pathinfo($input, PATHINFO_EXTENSION);
        }

        $input    = trim($input, '/');
        $required = array();
        $assets   = array();
        foreach ($this->locate($input, $type) as $asset) {
            if ($asset['include'] || !in_array($asset['file'], $required)) {
                $required[] = $asset['file'];
                $assets[]   = $asset;
            }
        }

        return $assets;
    }

    /**
     * Locate asset.
     *
     * @param string      $input   Pipeline alias
     * @param string      $type    Asset type (js, css)
     * @param Boolean     $include Is it include (or require)?
     * @param null|string $from    Alias of the parent asset
     *
     * @return array
     */
    protected function locate($input, $type, $include = false, $from = null)
    {
        $fileWithExtension = $input;
        if ($type !== pathinfo($fileWithExtension, PATHINFO_EXTENSION)) {
            $fileWithoutExtension = $fileWithExtension;
            $fileWithExtension   .= '.'.$type;
        } else {
            $fileWithoutExtension = substr($fileWithExtension, 0, 0 - strlen('.'.$type));
        }

        foreach ($this->paths as $path) {
            // directory index
            if (is_file($indexFile = $path.'/'.$type.'/'.$input.'/index.'.$type)) {
                return $this->processAssetsDirectives(array(array(
                    'include' => $include,
                    'file'    => str_replace($path.'/', '', $indexFile),
                    'filter'  => null,
                    'root'    => $path,
                )), $type);
            }

            // static asset
            if (is_file($assetFile = $path.'/'.$type.'/'.$fileWithExtension)) {
                return $this->processAssetsDirectives(array(array(
                    'include' => $include,
                    'file'    => str_replace($path.'/', '', $assetFile),
                    'filter'  => null,
                    'root'    => $path
                )), $type);
            }

            // preprocessor asset
            foreach ($this->filters as $filter) {
                if (!is_file($assetFile = $path.'/'.$type.'/'.$fileWithoutExtension.'.'.$filter)) {
                    continue;
                }

                return $this->processAssetsDirectives(array(array(
                    'include' => $include,
                    'file'    => str_replace($path.'/', '', $assetFile),
                    'filter'  => $filter,
                    'root'    => $path
                )), $type);
            }
        }

        throw new \RuntimeException(sprintf(
            'Asset "%s" could not be found anywhere in registered pipeline paths (%s)%s',
            $input, implode(', ', $this->paths), $from ? ' in asset: '.$from : ''
        ));
    }

    /**
     * Locates assets recursively.
     *
     * @param string $input Pipelined path alias
     * @param string $type  Assets type
     *
     * @return array
     */
    protected function locateTreeAssets($input, $type)
    {
        foreach ($this->paths as $path) {
            if (is_dir($assetsDir = $path.'/'.$type.'/'.$input)) {
                $assetFiles = Finder::create()
                    ->files()
                    ->name('/\.('.implode('|', $this->filters).'|'.$type.')$/')
                    ->ignoreVCS(true)
                    ->sortByName()
                ;

                $assets = array();
                foreach ($assetFiles->in($assetsDir) as $file) {
                    $filter = pathinfo($file, PATHINFO_EXTENSION);
                    if ($filter === $type) {
                        $filter = null;
                    }

                    $assets[] = array(
                        'include' => false,
                        'file'    => str_replace($path.'/', '', $file->getPathname()),
                        'filter'  => $filter,
                        'root'    => $path
                    );
                }

                if (count($assets)) {
                    return $assets;
                }
            }
        }
    }

    /**
     * Locates all assets in specific directory.
     *
     * @param string $input Pipelined path alias
     * @param string $type  Assets type
     *
     * @return array
     */
    protected function locateDirectoryAssets($input, $type)
    {
        foreach ($this->paths as $path) {
            if (is_dir($assetsDir = $path.'/'.$type.'/'.$input)) {
                $assetFiles = Finder::create()
                    ->files()
                    ->name('/\.('.implode('|', $this->filters).'|'.$type.')$/')
                    ->ignoreVCS(true)
                    ->sortByName()
                    ->depth(0)
                ;

                $assets = array();
                foreach ($assetFiles->in($assetsDir) as $file) {
                    $filter = pathinfo($file, PATHINFO_EXTENSION);
                    if ($filter === $type) {
                        $filter = null;
                    }

                    $assets[] = array(
                        'include' => false,
                        'file'    => str_replace($path.'/', '', $file->getPathname()),
                        'filter'  => $filter,
                        'root'    => $path
                    );
                }

                if (count($assets)) {
                    return $assets;
                }
            }
        }
    }

    /**
     * Processes directives inside asset files (require/include).
     *
     * There are 5 supported directives:
     *
     *   - require <alias>
     *   - include <alias>
     *   - require_directory <path_alias>
     *   - require_tree <path_alias>
     *   - require_self
     *
     * @param array  $assets Assets list
     * @param string $type   Assets type
     *
     * @return array
     */
    protected function processAssetsDirectives(array $assets, $type)
    {
        $keywords = 'require_directory|require_tree|require_self|require|include';
        $regex    = "/(?:\*=|\/\/=|\#=) *({$keywords})(?: +([^\n ]+))?/";

        $processed = array();
        foreach ($assets as $asset) {
            $selfProcessed = false;
            $assetContent  = file_get_contents($asset['root'].'/'.$asset['file']);

            preg_match_all($regex, $assetContent, $matches, PREG_SET_ORDER);
            foreach ($matches as $vals) {
                $directive = $vals[1];
                $path      = null;
                if (isset($vals[2])) {
                    $path = trim($vals[2]);
                }
                if (null !== $path && '.' === $path[0]) {
                    $dirname = dirname(substr($asset['file'], strlen($type)+1));

                    if ('.' === $dirname) {
                        $dirname = '';
                    }

                    $path = ltrim($dirname.substr($path, 1), '/');
                }

                switch ($directive) {
                    case 'require':
                        $loaded = $this->locate($path, $type, false, $asset['file']);
                        break;
                    case 'include':
                        $loaded = $this->locate($path, $type, true, $asset['file']);
                        break;
                    case 'require_directory':
                        $loaded = $this->locateDirectoryAssets($path, $type);
                        break;
                    case 'require_tree':
                        $loaded = $this->locateTreeAssets($path, $type);
                        break;
                    case 'require_self':
                        if (!$selfProcessed) {
                            $selfProcessed = true;
                            $loaded = array($asset);
                        }
                        break;
                }

                foreach ($loaded as $loadedAsset) {
                    $processed[] = $loadedAsset;
                }
            }

            if (!$selfProcessed) {
                $processed[] = $asset;
            }
        }

        return $processed;
    }
}
