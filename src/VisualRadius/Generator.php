<?php
Namespace VisualRadius
class Generator
{
    private $_renderer;
    private $_dataSource;
    private $_options;

    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    public function setRenderer(IRenderer $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    public function setSource(IDataSource $dataSource)
    {
        $this->_dataSource = $dataSource;
        return $this;
    }

    public function generate()
    {
        if (!isset($this->_renderer)) {
            throw new BadMethodCallException('No Renderer set');
        }

        if (!isset($this->_dataSource)) {
            throw new BadMethodCallException('No Source set');
        }

        $sessions = $this->_dataSource->getData();

        $data = VisualRadius_PreRenderedData::buildFromSessionData($sessions, $this->_options);

        return $this->_renderer->render($data);
    }
}