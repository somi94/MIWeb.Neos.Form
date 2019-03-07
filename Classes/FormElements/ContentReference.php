<?php
namespace MIWeb\Neos\Form\FormElements;

use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMapper;
use Neos\Form\Core\Model\FormElementInterface;
use Neos\Form\Core\Runtime\FormRuntime;
use Neos\Form\Core\Model\AbstractFormElement;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;
use Neos\ContentRepository\Domain\Repository\WorkspaceRepository;
use Neos\ContentRepository\Domain\Model\NodeData;
use Neos\ContentRepository\Domain\Service\Context;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\Eel\FlowQuery\FlowQuery;

class ContentReference extends AbstractFormElement {
    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

//    /**
//     * @var NodeDataRepository
//     * @Flow\Inject
//     */
//    protected $nodeRepository;
//
//    /**
//     * @var WorkspaceRepository
//     * @Flow\Inject
//     */
//    protected $workspaceRepository;

//    /**
//     * @return void
//     */
//    public function initializeFormElement() {
//        if($this->properties['dataType'] === 'Node') {
//            $this->setDataType(NodeData::class);
//        }
//    }

    /**
     * Generates the input options
     * TODO: apply parent filter
     * TODO: convert to node on submit
     *
     * @param FormRuntime $formRuntime
     * @return void
     */
    public function beforeRendering(FormRuntime $formRuntime) {
        $parent = isset($this->properties['parent']) ? $this->properties['parent'] : null;
        $type = isset($this->properties['type']) ? $this->properties['type'] : null;
        $titleProperty = isset($this->properties['titleProperty']) ? $this->properties['titleProperty'] : 'title';

        $context = $this->contextFactory->create();

        $q = new FlowQuery([$context->getCurrentSiteNode()]);
        $nodes = $q->find('[instanceof ' . $type . ']')->sort('date', 'DESC')->get();

        $options = [];
        if(isset($this->properties['default'])) {
            if($this->properties['dataType'] === 'Node') {
                $options[''] = $this->properties['default'];
            } else {
                $options[$this->properties['default']] = $this->properties['default'];
            }
        }
        foreach($nodes as $node) {
            if($this->properties['dataType'] === 'Node') {
                $options[$node->getIdentifier()] = $node->getProperty($titleProperty);
            } else {
                $options[$node->getProperty($titleProperty)] = $node->getProperty($titleProperty);
            }
        }

        $this->setProperty('options', $options);
    }
}