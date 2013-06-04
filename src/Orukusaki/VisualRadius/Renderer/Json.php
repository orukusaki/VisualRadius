<?php
/**
 * Contains the Json class
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace Orukusaki\VisualRadius\Renderer;

use Orukusaki\VisualRadius\Data\PreRenderedData;
use Doctrine\ODM\MongoDB\PersistentCollection;

/**
 * Dumps image data as JSON
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class Json implements RendererInterface
{
    private $uow;
    private $urlGenerator;

    /**
     * Constructor
     *
     * @param ArrayAccess $container Container
     *
     * @return void
     */
    public function __construct(\ArrayAccess $container)
    {
        $this->uow = $container['doctrine.odm.mongodb.dm']->getUnitOfWork();
        $this->urlGenerator = $container['url_generator'];
    }

    /**
     * Render
     *
     * @param PreRenderedData $object Data to render
     *
     * @return Closure
     */
    public function render(PreRenderedData $object)
    {
        $data = $this->getData($object);

        $data = $this->addLinks($data);

        return function () use ($data) {
             echo json_encode($data);
        };
    }

    /**
     * Get Data
     *
     * Recursively extracts data from an object
     *
     * @param object $object Object to extract
     *
     * @return array
     */
    private function getData($object)
    {
        $data = $this->uow->getDocumentActualData($object);

        foreach ($data as $key => $property) {
            if ($property instanceof PersistentCollection) {
                $data[$key] = $property->toArray();

                foreach ($data[$key] as $subkey => $subproperty) {
                    if (is_object($subproperty)) {
                        $data[$key][$subkey] = $this->getData($subproperty);
                        $data[$key][$subkey]['type'] = preg_replace(
                            "~Orukusaki\\\\VisualRadius\\\\Data\\\\(Slot|Session)~",
                            '',
                            get_class($subproperty)
                        );
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Get Content Header
     *
     * @return array
     */
    public function getContentHeader()
    {
        return array("Content-type" => "application/json");
    }

    /**
     * Add Links
     *
     * @param array $data Data
     *
     * @return array
     */
    private function addLinks(array $data)
    {
        $data['_links'] = array(
            'home' => array(
                'href' => $this->urlGenerator->generate('home', array(), true)
            ),
            'self' => array(
                'href' => $this->urlGenerator->generate('viewImage', array('imageId' => $data['id']), true)
            ),
        );

        return $data;
    }
}
