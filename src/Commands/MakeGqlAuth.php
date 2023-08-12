<?php

namespace kmacute\GqlHelper\Commands;

use Illuminate\Console\Command;
use kmacute\GqlHelper\Commands\Concerns\MakeGql\FolderTrait;
use kmacute\GqlHelper\Commands\Concerns\MakeGql\StubTrait;

class MakeGqlAuth extends Command
{
    use FolderTrait, StubTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:gql-auth
    {--force : This will force replace the records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Graphql type, query, mutation';

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->createAuthGraph();
        $this->createLoginFunction();
    }

    /**
     * Create auth.graph
     * 
     * @return void
     */
    public function createAuthGraph()
    {
        $modelAuthTemplate = $this->getStub('auth.graphql');
        $request_path = $this->createGraphFolder('auth');
        if ($request_path == '') return;
        file_put_contents($request_path, $modelAuthTemplate);
        echo ("auth.graphql has been created! \r\n");
    }

    /**
     * Create Login Mutation
     * 
     * @return void
     */
    public function createLoginFunction()
    {
        $modelAuthTemplate = $this->getStub('LoginMutation');
        $request_path = $this->createMutationFolder('Login');
        if ($request_path == '') return;

        file_put_contents($request_path, $modelAuthTemplate);
        echo ("Login Mutation has been created! \r\n");
    }
}
