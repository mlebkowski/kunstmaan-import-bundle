<?php

namespace Nassau\KunstmaanImportBundle\Process;

class ConfigurableIterator implements \IteratorAggregate
{
    /**
     * @var \Traversable
     */
    private $iterator;

    /**
     * @var array
     */
    private $names;

    public function __construct(\Traversable $iterator, array $names)
    {
        $this->iterator = $iterator;
        $this->names = $names;
    }

    public function getIterator()
    {
        foreach ($this->names as $offset) {
            if (false === isset($this->iterator[$offset])) {
                throw new \InvalidArgumentException(sprintf(
                    'There is no post processor by the name %s configured. Available names: %s',
                    $offset, implode(', ', array_keys(iterator_to_array($this->iterator)))
                ));
            }

            yield $this->iterator[$offset];
        }
    }
}
