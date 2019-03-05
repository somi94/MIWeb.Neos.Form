<?php
namespace MIWeb\Neos\Form\FormElements;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMapper;
use Neos\Form\Core\Model\FormElementInterface;
use Neos\Form\Core\Runtime\FormRuntime;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Form\FormElements\FileUpload as NeosFileUpload;

class FileUpload extends NeosFileUpload {
    /**
     * @var PropertyMapper
     * @Flow\Inject
     */
    protected $propertyMapper;

    /**
     *
     * @param FormRuntime $formRuntime
     * @param mixed $elementValue
     * @return void
     */
    public function onSubmit(FormRuntime $formRuntime, &$elementValue) {
        //TODO: validate resource types
        //TODO: validate file count / not empty

        //parent::onSubmit($formRuntime, $elementValue);

        $request = $formRuntime->getRequest();
        $arg = $this->identifier;
        if(!$request->hasArgument($arg)) {
            return;
        }

        $c = 0;
        foreach($request->getArgument($arg) as $fileInfo) {
            //echo 'file:<br>';
            //var_dump($fileInfo);
            $resource = $this->propertyMapper->convert($fileInfo, PersistentResource::class);
            //var_dump($resource);
            $formRuntime[$arg . '__' . $c] = $resource;
            $c++;
        }
    }
}