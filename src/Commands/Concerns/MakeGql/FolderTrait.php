<?php

namespace kmacute\GqlHelper\Commands\Concerns\MakeGql;

use Illuminate\Support\Facades\DB;

trait FolderTrait
{
    /**
     * Create a folder for validator
     * 
     * @return string
     */
    public function createValidatorFolder()
    {
        if (!file_exists($path = app_path('/GraphQL')))
            mkdir($path, 0777, true);

        if (!file_exists($path = app_path('/GraphQL/Validators')))
            mkdir($path, 0777, true);

        $request_path = app_path("/GraphQL/Validators/{$this->modelName}InputValidator.php");
        if (file_exists($request_path)) {
            if (!$this->option('force')) {
                echo ("{$this->modelName}InputValidator.php already exists! \r\n");
                return '';
            }
        }

        return $request_path;
    }

    /**
     * Create a folder for validator
     * 
     * @return string
     */
    public function createMutationFolder($functionName)
    {
        if (!file_exists($path = app_path('/GraphQL')))
            mkdir($path, 0777, true);

        if (!file_exists($path = app_path('/GraphQL/Mutations')))
            mkdir($path, 0777, true);

        $request_path = app_path("/GraphQL/Mutations/{$functionName}.php");
        if (file_exists($request_path)) {
            if (!$this->option('force')) {
                echo ("{$functionName}.php already exists! \r\n");
                return '';
            }
        }

        return $request_path;
    }

    /**
     * Create graphql file and folder
     * 
     * @param string $modelName
     * 
     * @return string
     */
    public function createGraphFolder($modelName)
    {
        if (!file_exists($path = base_path('/graphql')))
            mkdir($path, 0777, true);

        $request_path = base_path("/graphql/{$modelName}.graphql");
        if (file_exists($request_path)) {
            if (!$this->option('force')) {
                echo ("{$modelName}.graphql already exists! \r\n");
                return '';
            }
        }

        return $request_path;
    }

    /**
     * Create model folder and file
     * 
     * @return string
     */
    public function createModelFolder()
    {
        $request_path = app_path("Models/{$this->modelName}.php");
        if (file_exists($request_path)) {
            if (!$this->option('force')) {
                echo ("{$this->modelName}.php Model already exists! \r\n");
                return '';
            }
        }

        return $request_path;
    }
}
