<?php

namespace kmacute\GqlHelper\Commands\Concerns\MakeGql;

use Illuminate\Support\Facades\DB;

trait SchemaTrait
{
    public function getSchema()
    {
        $tableName = strtolower($this->argument('name'));
        $database = env('DB_DATABASE', '');
        return  DB::select("
            SELECT  COLUMN_NAME,
                    Column_Default,
                    Column_Key,
                    IS_NULLABLE,
                    DATA_TYPE,
                    COALESCE(Character_Maximum_Length,Numeric_Precision) AS Column_Size
            FROM    INFORMATION_SCHEMA.Columns
            WHERE   table_name = '$tableName'
                    AND table_schema = '$database'
        ");
    }
}
