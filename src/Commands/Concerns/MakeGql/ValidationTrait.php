<?php

namespace kmacute\GqlHelper\Commands\Concerns\MakeGql;

use Illuminate\Support\Facades\DB;

trait ValidationTrait
{
    /**
     * Get validation fields
     * 
     * @return string
     */
    public function getValidationFields()
    {
        $ValidationFields = [];
        for ($i = 0; $i < count($this->schema); $i++) {
            switch ($this->schema[$i]->COLUMN_NAME) {
                case 'id':
                case 'created_by':
                case 'updated_by':
                case 'deleted_by':
                case 'created_at':
                case 'updated_at':
                case 'deleted_at':
                    break;

                default:
                    $result = $this->getFieldForValidation($this->schema[$i]);
                    $ValidationFields[] = "            $result";
                    break;
            }
        }

        return implode("\r\n", $ValidationFields);
    }

    /**
     * Get fields for validation
     * 
     * @param object $field
     * 
     * @return string
     */
    private function getFieldForValidation($field)
    {
        $DATA_TYPE = '';
        $IS_NULLABLE = '';
        $Column_Size = '';

        if ($field->IS_NULLABLE) {
            $IS_NULLABLE = ($field->IS_NULLABLE == 'YES' ? "'nullable'," : "'required',");
        }

        switch ($field->DATA_TYPE) {
            case 'varchar':
            case 'char':
                $DATA_TYPE = "";
                $Column_Size = "'max:$field->Column_Size'";
                break;

            case 'decimal':
            case 'int':
                $DATA_TYPE = "'numeric',";
                $Column_Size = "'digits_between:0,$field->Column_Size'";
                break;

            case 'tinyint':
                $DATA_TYPE = "'boolean',";
                break;

            case 'datetime':
            case 'date':
                $DATA_TYPE = '';
                break;
        }

        return "'" . $field->COLUMN_NAME . "' => [" . $IS_NULLABLE . $DATA_TYPE . $Column_Size . '],';
    }
}
