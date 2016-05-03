<?php

namespace Finwo\Datatools;

use Finwo\PropertyAccessor\PropertyAccessor;

class ArrayQuery
{
    /**
     * @var array
     */
    protected $table = array();

    /**
     * @var array
     */
    protected $selects = array();

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @var string
     */
    protected $selectedField = '';

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var integer
     */
    protected $skip = 0;

    /**
     * @var integer
     */
    protected $limit = -2;

    /**
     * @var array
     */
    protected $columnNames = array();

    /**
     * ArrayQuery constructor.
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->table = $data;
    }

    /**
     * @param array $input
     *
     * @return ArrayQuery
     */
    public static function table($input = array())
    {
        return new ArrayQuery($input);
    }

    /**
     * @param $input
     *
     * @return ArrayQuery $this
     */
    public function select( $input )
    {
        if (
            is_callable($input) ||
            is_string($input)
        ) {
            $this->selects[] = $input;
        } elseif (is_array($input)) {
            foreach($input as $item) {
                $this->select($item);
            }
        }

        return $this;
    }

    /**
     * @param $input
     *
     * @return $this
     */
    public function field( $input )
    {
        if(is_string($input)) {
            $this->selectedField = $input;
        }

        return $this;
    }

    /**
     * @param $input
     *
     * @return $this
     */
    public function equals( $input )
    {
        if(is_string($input)) {
            $this->filters[]= array(
                'field' => $this->selectedField,
                'type'  => 'equals',
                'value' => $input,
            );
        }

        return $this;
    }

    /**
     * @param $input
     *
     * @return $this
     */
    public function validates( $input )
    {
        if(is_callable($input)) {
            $this->filters[]= array(
                'field' => $this->selectedField,
                'type'  => 'validate',
                'value' => $input,
            );

        }

        return $this;
    }

    /**
     * @param $input
     *
     * @return $this
     */
    public function columnName( $input )
    {
        if (is_array($input)) {
            $this->columnNames = $input;
        }

        if (is_string($input)) {
            $this->columnNames[] = $input;
        }

        return $this;
    }

    /**
     * @param integer $input
     *
     * @return $this
     */
    public function skip( $input )
    {
        $this->skip = intval($input);
        return $this;
    }

    /**
     * @param integer $input
     *
     * @return $this
     */
    public function limit( $input )
    {
        $this->limit = intval($input);
        return $this;
    }

    /**
     * @return PropertyAccessor
     */
    protected function getAccessor()
    {
        if (is_null($this->accessor)) {
            $this->accessor = new PropertyAccessor();
        }
        return $this->accessor;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $outputIndex = 0;
        $output = array();

        //loop through table
        foreach ($this->table as $row) {

            // handle limit
            if ( $this->limit-- == 0 ) {
                break;
            }

            // reset the current row
            $columnIndex = 0;
            $outputRow   = array();

            // fetch columns
            foreach ($this->selects as $select) {

                // allow associative column names
                $colName = $columnIndex++;
                if (isset($this->columnNames[$colName])) {
                    $colName = $this->columnNames[$colName];
                }

                // fetch & store value
                $outputRow[$colName] = $this->executeSelect($row, $select);
            }

            // run through filters
            if ( $this->executeFilters($outputRow) ) {

                // handle offset
                if ( $this->skip-- > 0) {
                    continue;
                }

                // add row to output
                $output[$outputIndex++] = $outputRow;
            } else {
                $this->limit++;
            }

        }

        return $output;
    }

    protected function executeSelect($row, $select)
    {
        // Callable select statements
        if (is_callable($select)) {
            return call_user_func($select, $row);
        }

        // String selects
        if (is_string($select)) {
            return $this->getAccessor()->get($row, $select);
        }

        // Return empty column
        return null;
    }

    protected function executeFilters($row)
    {
        foreach ($this->filters as $filter) {
            switch($filter['type']) {
                case 'equals':
                    if ($row[$filter['field']] !== $filter['value']) {
                        return false;
                    }
                    break;
                case 'validate':
                    return call_user_func($filter['value'], $row[$filter['field']], $row);
                default:
                    //unknown filter
                    break;
            }

        }

        return true;
    }
}
