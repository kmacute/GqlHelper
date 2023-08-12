<?php

namespace kmacute\GqlHelper\Commands\Concerns\MakeGql;

use Illuminate\Support\Facades\DB;

trait StubTrait
{
    public function getStub($type)
    {
        return file_get_contents(dirname(__DIR__, 2) .  "/Stubs/$type.stub");
    }
}
