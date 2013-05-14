<?php
/**
 * Contains the Gd class
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
namespace VisualRadius\Renderer;

use VisualRadius\Data\PreRenderedData;

/**
 * Renderer generates a png image using the gd library
 *
 * @package    VisualRadius
 * @subpackage Renderer
 * @author     Peter Smith <peter@orukusaki.co.uk>
 */
class Json implements RendererInterface
{
    private $uow;

    public function __construct($app, array $options)
    {
        $this->uow = $app['doctrine.odm.mongodb.dm']->getUnitOfWork();
    }

    public function render(PreRenderedData $object)
    {
        $data = $this->uow->getDocumentActualData($object);
        $data['slots'] = $data['slots']->toArray();

        foreach ($data['slots'] as $i => $slot) {

            $data['slots'][$i] = $this->uow->getDocumentActualData($slot);
            $data['slots'][$i]['type'] = str_replace(
                'VisualRadius\\Data\\Slot',
                '',
                get_class($slot)
            );

            if (!array_key_exists('objects', $data['slots'][$i])) {
                continue;
            }

            $data['slots'][$i]['objects'] = $data['slots'][$i]['objects']->toArray();

            foreach ($data['slots'][$i]['objects'] as $j => $session) {

                $data['slots'][$i]['objects'][$j] = $this->uow->getDocumentActualData($session);
                $data['slots'][$i]['objects'][$j]['type'] = str_replace(
                    'VisualRadius\\Data\\Session',
                    '',
                    get_class($session)
                );
            }
        }

        return function () use ($data) {
             echo json_encode($data);
        };
    }

    public function getContentHeader()
    {
        return array("Content-type" => "text/json");
    }

}