<?php
namespace MIWeb\Neos\Form\FormElements;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMapper;
use Neos\Form\Core\Model\FormElementInterface;
use Neos\Form\Core\Runtime\FormRuntime;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Form\FormElements\FileUpload as NeosFileUpload;
use Neos\Form\Validation\FileTypeValidator;
use Neos\Flow\Validation\Validator\NotEmptyValidator;
use \Neos\Flow\Property\PropertyMappingConfiguration;
use Neos\Flow\Property\TypeConverter\TypedArrayConverter;
use Neos\Error\Messages\Error;

class FileUpload extends NeosFileUpload {
    /**
     * @var PropertyMapper
     * @Flow\Inject
     */
    protected $propertyMapper;
    /**
     * @return void
     */
    public function initializeFormElement() {
        /*$formDefinition = $this->getRootForm();
        $processingRule = $formDefinition->getProcessingRule($this->identifier);
        $mappingConfig = $processingRule->getPropertyMappingConfiguration();

        $typeConverter = new TypedArrayConverter();

        $mappingConfig->setTypeConverter($typeConverter);
        $this->setDataType('array<Neos\Flow\ResourceManagement\PersistentResource>');*/

        //$this->setDataType('array');
        $this->setDataType(PersistentResource::class);
    }

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

        $formDefinition = $this->getRootForm();

        $processingRule = $formDefinition->getProcessingRule($arg);

        if(!$request->hasArgument($arg)) {
            if(isset($this->properties['minFiles']) && $this->properties['minFiles'] > 0) {
                $processingRule->getProcessingMessages()->addError(new Error("Please upload at least %s files.", 1552470144, [$this->properties['minFiles']]));
            }
            return;
        }

        $fileTypeValidator = new FileTypeValidator(array('allowedExtensions' => $this->properties['allowedExtensions']));
        $processingRule->addValidator($fileTypeValidator);

        //$notEmptyValidator = new NotEmptyValidator();
        //$processingRule->addValidator($notEmptyValidator);

        $c = 0;
        foreach($request->getArgument($arg) as $fileInfo) {
            //$resource = $this->propertyMapper->convert($fileInfo, PersistentResource::class);
            $resource = $processingRule->process($fileInfo);

            /*if($c == 0) {
                $formRuntime[$arg] = $resource;
            }*/
            $formRuntime[$arg] = '1';

            $fileIdentifier = $arg . '__' . $c;

            $formRuntime[$fileIdentifier] = $resource;

            $c++;
        }

        if(isset($this->properties['minFiles']) && $c < $this->properties['minFiles']) {
            //throw new \ErrorException("Please upload at least %s files.", 1552470144, [$this->properties['minFiles']]);
            $processingRule->getProcessingMessages()->addError(new Error("Please upload at least %s files.", 1552470144, [$this->properties['minFiles']]));
        } else if(isset($this->properties['maxFiles']) && $c > $this->properties['maxFiles']) {
            $processingRule->getProcessingMessages()->addError(new Error("Please upload a maximum of %s files.", 1552470179, [$this->properties['maxFiles']]));
        }
    }
}