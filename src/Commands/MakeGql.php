<?php

namespace kmacute\GqlHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Str;

class MakeGql extends Command
{
    protected $tableName;
    protected $modelName;
    protected $schema;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:gql
    {name : Class (singular) for example User}
    {--m : m for model creation}
    {--force : This will force replace the records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Graphql type, query, mutation';

    public function __construct()
    {
        $this->tableName = "";
        $this->modelName = "";
        $this->schema = [];
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->tableName = strtolower(Str::plural($this->argument('name')));
        $this->modelName = Str::singular(Str::studly($this->tableName));
        $this->schema = $this->getSchema();

        $this->createValidation();
        $this->createType();
        if ($this->option('m')) {
            $this->createModel();
        }
    }



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

    public function getStub($type)
    {
        return file_get_contents(__DIR__ .  "/Stubs/$type.stub");
    }

    public function createModel()
    {
        $fillableFields = $this->getFillableFields();
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{fillableFields}}'
            ],
            [
                $this->modelName,
                $this->tableName,
                $fillableFields
            ],
            $this->getStub('Model')
        );

        $request_path = app_path("Models/{$this->modelName}.php");
        if (file_exists($request_path)) {
            if (!$this->option('force')) {
                echo ("{$this->modelName}.php Model already exists! \r\n");
                return;
            }
        }

        file_put_contents($request_path, $modelTemplate);
        echo ("{$this->modelName}.php Model created! \r\n");
    }

    private function getFillableFields()
    {
        $list = $this->schema;
        $ValidationFields = [];
        for ($i = 0; $i < count($list); $i++) {
            switch ($list[$i]->COLUMN_NAME) {
                case 'id':
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

    public function createValidation()
    {
        $validationFields = $this->getValidationFields();
        $requestTemplate = str_replace(
            ['{{modelName}}', '{{fields}}',],
            [$this->modelName, $validationFields],
            $this->getStub('Validator')
        );

        if (!file_exists($path = app_path('/GraphQL')))
            mkdir($path, 0777, true);

        if (!file_exists($path = app_path('/GraphQL/Validators')))
            mkdir($path, 0777, true);

        $request_path = app_path("/GraphQL/Validators/{$this->modelName}InputValidator.php");
        if (file_exists($request_path)) {
            if (!$this->option('force')) {
                echo ("{$this->modelName}InputValidator.php already exists! \r\n");
                return;
            }
        }
        file_put_contents($request_path, $requestTemplate);
        echo ("{$this->modelName}InputValidator.php created! \r\n");
    }

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
                    $result = $this->getValidationField($this->schema[$i]);
                    $ValidationFields[] = "            $result";
                    break;
            }
        }

        return implode("\r\n", $ValidationFields);
    }

    private function getValidationField($list)
    {
        $DATA_TYPE = '';
        $IS_NULLABLE = '';
        $Column_Size = '';

        if ($list->IS_NULLABLE) {
            $IS_NULLABLE = ($list->IS_NULLABLE == 'YES' ? "'nullable'," : "'required',");
        }

        switch ($list->DATA_TYPE) {
            case 'varchar':
            case 'char':
                $DATA_TYPE = "";
                $Column_Size = "'max:$list->Column_Size'";
                break;

            case 'decimal':
            case 'int':
                $DATA_TYPE = "'numeric',";
                $Column_Size = "'digits_between:0,$list->Column_Size'";
                break;

            case 'tinyint':
                $DATA_TYPE = "'boolean',";
                break;

            case 'datetime':
            case 'date':
                $DATA_TYPE = '';
                break;
        }

        return "'" . $list->COLUMN_NAME . "' => [" . $IS_NULLABLE . $DATA_TYPE . $Column_Size . '],';
    }

    private function createType()
    {
        if (!file_exists($path = base_path('/graphql')))
            mkdir($path, 0777, true);

        $typeFields = $this->getTypeFields();
        $typeInputFields = $this->getTypeInputFields();
        $modelLowercase = Str::lower($this->modelName);
        $modelPlural = Str::plural($this->modelName);

        $template = str_replace(
            ['{{modelName}}', '{{typeFields}}', '{{typeInputFields}}', '{{modelNamePlural}}',],
            [$this->modelName, $typeFields, $typeInputFields, $modelPlural],
            $this->getStub('Graphql')
        );


        $request_path = base_path("/graphql/{$modelLowercase}.graphql");
        if (file_exists($request_path)) {
            if (!$this->option('force')) {
                echo ("{$modelLowercase}.graphql already exists! \r\n");
                return;
            }
        }

        file_put_contents($request_path, $template);
        echo ("{$modelLowercase}.graphql created! \r\n");
    }

    private function getTypeFields()
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

    private function getTypeInputFields()
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
}
