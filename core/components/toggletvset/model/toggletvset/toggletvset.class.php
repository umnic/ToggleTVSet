<?php

/**
 * Main Class for ToggleTVSet
 *
 * @package toggletvset
 * @subpackage class
 *
 * @author info@pepebe.de,
 * @author thomas.jakobi@partout.info
 */
class ToggleTVSet
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'toggletvset';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.0.0';

    /**
     * The class options
     * @var array $options
     */
    public $options = array();

    /**
     * ToggleTVSet constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    function __construct(modX &$modx, $options = array())
    {
        $this->modx = &$modx;

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/' . $this->namespace . '/');

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'version' => $this->version,
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'connectorUrl' => $assetsUrl . 'connector.php',
        ), $options);

        // set default options
        $toggleTvs = $this->getOption('toggletvs');
        $this->options = array_merge($this->options, array(
            'debug' => (boolean)$this->getOption('debug'),
            'toggletvs' => ($toggleTvs) ? array_map('trim', explode(',', $toggleTvs)) : array()
        ));

        $this->modx->lexicon->load($this->namespace . ':default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Register the client scripts
     */
    public function regClientScripts()
    {
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">' .
            'var ToggleTVSet = {"options": ' . json_encode(array(
                'debug' => $this->getOption('debug'),
                'toggletvs' => $this->getOption('toggletvs')
            )) . '};' . '</script>');
        $this->modx->regClientStartupScript($this->options['assetsUrl'] . 'mgr/js/toggletvset.js?v=v' . $this->version);
    }
}