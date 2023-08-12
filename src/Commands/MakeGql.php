<?php

namespace kmacute\GqlHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use kmacute\GqlHelper\Commands\Concerns\MakeGql\FieldTrait;
use kmacute\GqlHelper\Commands\Concerns\MakeGql\FolderTrait;
use kmacute\GqlHelper\Commands\Concerns\MakeGql\SchemaTrait;
use kmacute\GqlHelper\Commands\Concerns\MakeGql\StubTrait;
use kmacute\GqlHelper\Commands\Concerns\MakeGql\ValidationTrait;

class MakeGql extends Command
{
    use SchemaTrait, FieldTrait, ValidationTrait, FolderTrait, StubTrait;

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
        $this->createGraphql();
        if ($this->option('m')) {
            $this->createModel();
        }
    }

    public function createValidation()
    {
        $validationFields = $this->getValidationFields();

        $requestTemplate = str_replace(
            ['{{modelName}}', '{{fields}}',],
            [$this->modelName, $validationFields],
            $this->getStub('Validator')
        );

        $request_path = $this->createValidatorFolder();

        if ($request_path == '') return; // path already exist, user did not want to create a new file

        file_put_contents($request_path, $requestTemplate);

        echo ("{$this->modelName}InputValidator.php created! \r\n");
    }

    private function createGraphql()
    {
        $typeFields = $this->getTypeFields();
        $typeInputFields = $this->getTypeInputFields();
        $modelLowercase = Str::lower($this->modelName);
        $modelPlural = Str::plural($this->modelName);

        $template = str_replace(
            ['{{modelName}}', '{{typeFields}}', '{{typeInputFields}}', '{{modelNamePlural}}',],
            [$this->modelName, $typeFields, $typeInputFields, $modelPlural],
            $this->getStub('Graphql')
        );

        $request_path = $this->createGraphFolder($modelLowercase);
        if ($request_path == '') return;

        file_put_contents($request_path, $template);
        echo ("{$modelLowercase}.graphql created! \r\n");
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
            $this->getStub($this->tableName == 'users' ? 'user.model' : 'Model')
        );

        $request_path = $this->createModelFolder();

        file_put_contents($request_path, $modelTemplate);
        echo ("{$this->modelName}.php Model created! \r\n");
    }

    public function createAuth()
    {
        // create auth.graphql
        $modelAuthTemplate = $this->getStub('auth.graphql');
        $request_path = $this->createGraphFolder('auth');
        if ($request_path == '') return;

        file_put_contents($request_path, $modelAuthTemplate);
        echo ("auth.graphql has been created! \r\n");

        // create Login function
        $modelAuthTemplate = $this->getStub('LoginMutation');
        $request_path = $this->createMutationFolder('Login');
        if ($request_path == '') return;


        file_put_contents($request_path, $modelAuthTemplate);
        echo ("Login Mutation has been created! \r\n");
    }
}
