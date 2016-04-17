<?php

namespace Finwo\Datatools;

use Finwo\PropertyAccessor\PropertyAccessor;

class Mappable implements \Iterator
{
    /**
     * @var array
     */
    protected $indexes = array();

    /**
     * @var integer
     */
    protected $currentIndex = 0;

    /**
     * @var PropertyAccessor
     */
    protected $accessor = null;

    /**
     * Mappable constructor.
     * @param null $data
     */
    public function __construct( $data = null )
    {
        // Build index
        $reflect = new \ReflectionClass($this);
        $this->indexes = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

        // Map stuff if needed
        if ( is_array($data) || is_object($data) ) {
            foreach($data as $key => $value) {
                $this->getPropertyAccessor()->set($this, $key, $value);
            }
        }
    }

    /**
     * @return PropertyAccessor
     */
    protected function getPropertyAccessor()
    {
        if (! $this->accessor instanceof PropertyAccessor ) {
            // Run in debug mode, because we might have inaccessible stuff
            $this->accessor = new PropertyAccessor(true);
        }
        return $this->accessor;
    }

    /**
     * @return array|mixed|null
     * @throws \Exception
     */
    public function current()
    {
        $name = $this->indexes[$this->currentIndex]->name;
        return $this->getPropertyAccessor()->get($this, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->currentIndex ++;
    }

    /**
     * @return integer
     */
    public function key()
    {
        return $this->currentIndex;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return isset($this->indexes[$this->currentIndex]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->currentIndex = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function reverse()
    {
        $this->indexes = array_reverse($this->indexes);
        $this->rewind();
    }
}
