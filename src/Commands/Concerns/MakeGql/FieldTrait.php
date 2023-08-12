<?php

namespace kmacute\GqlHelper\Commands\Concerns\MakeGql;

use Illuminate\Support\Facades\DB;

trait FieldTrait
{
    /**
     * Get fields for Graphql
     * 
     * @return string
     */
    public function getTypeFields()
    {
        $fields = [];
        for ($i = 0; $i < count($this->schema); $i++) {
            switch ($this->schema[$i]->COLUMN_NAME) {
                case 'id':
                    $fields[] = "id: ID";
                    break;

                default:
                    $fieldName = $this->schema[$i]->COLUMN_NAME;
                    $fieldType = in_array($this->schema[$i]->DATA_TYPE, ["decimal", "int"]) ? 'Int' : 'String';
                    $fields[] = "$fieldName: $fieldType";
                    break;
            }
        }

        return implode("\r\n    ", $fields);
    }

    /**
     * Get fields for Graphql Input
     * 
     * @return string
     */
    public function getTypeInputFields()
    {
        $fields = [];
        for ($i = 0; $i < count($this->schema); $i++) {
            switch ($this->schema[$i]->COLUMN_NAME) {
                case 'id':
                    $fields[] = "id: Int";
                    break;

                case 'created_by':
                case 'updated_by':
                case 'deleted_by':
                case 'created_at':
                case 'updated_at':
                case 'deleted_at':
                    break;

                default:
                    $fieldName = $this->schema[$i]->COLUMN_NAME;
                    $fieldType = in_array($this->schema[$i]->DATA_TYPE, ["decimal", "int"]) ? 'Int' : 'String';
                    $fields[] = "$fieldName: $fieldType";
                    break;
            }
        }

        return implode("\r\n    ", $fields);
    }

    /**
     * Get fillable fields for model
     * 
     * @return string
     */
    public function getFillableFields()
    {
        $list = $this->schema;
        $ValidationFields = [];
        for ($i = 0; $i < count($list); $i++) {
            switch ($list[$i]->COLUMN_NAME) {
                case 'id':
                case 'password':
                case 'created_by':
                case 'updated_by':
                case 'deleted_by':
                case 'created_at':
                case 'updated_at':
                case 'deleted_at':
                    break;

                default:
                    $result = $list[$i]->COLUMN_NAME;
                    $ValidationFields[] = "      '$result'";
                    break;
            }
        }
        return implode(",\r\n", $ValidationFields);
    }
}
