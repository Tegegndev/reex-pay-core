<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeBackendController extends Command
{
    protected $signature = 'make:backend-controller {name}';

    protected $description = 'Create a backend controller that extends BaseController with permission mapping';

    public function handle(): void
    {
        $name           = Str::studly($this->argument('name'));
        $controllerPath = app_path("Http/Controllers/Backend/{$name}.php");

        if (File::exists($controllerPath)) {
            $this->error("Controller already exists at: {$controllerPath}");

            return;
        }

        $stub = $this->getStub($name);

        File::ensureDirectoryExists(app_path('Http/Controllers/Backend'));
        File::put($controllerPath, $stub);

        $this->info("Backend controller created: {$controllerPath}");
    }

    protected function getStub(string $class): string
    {
        $baseName   = str_replace('Controller', '', $class);
        $modelVar   = Str::camel($baseName);
        $modelClass = "App\\Models\\{$baseName}";

        return <<<PHP
<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class {$class} extends BaseController
{
    public  static function permissions(): array
    {
        return [
            'index'           => '{$modelVar}-list',
            'create|store'    => '{$modelVar}-create',
            'edit|update'     => '{$modelVar}-edit',
            'destroy'         => '{$modelVar}-delete',
        ];
    }

    public function index(): View
    {
        return view('backend.{$modelVar}.index');
    }

    public function create(): View
    {
        return view('backend.{$modelVar}.create');
    }

    public function store(Request \$request)
    {
        //
    }

    public function edit(\$id): View
    {
        return view('backend.{$modelVar}.edit', compact('id'));
    }

    public function update(Request \$request, \$id)
    {
        //
    }

    public function destroy(\$id)
    {
        //
    }
}
PHP;
    }
}
