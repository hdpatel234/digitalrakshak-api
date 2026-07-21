<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateTableComponents extends Command
{
    protected $signature = 'make:table-components 
                            {table : The name of the table}
                            {--columns= : Comma-separated list of columns (optional)}';

    protected $description = 'Generate Model, Repository, and Service for a table with constants and getter functions';

    protected $baseModel = 'BaseModel';
    protected $baseRepository = 'BaseRepository';
    protected $baseService = 'BaseService';

    public function handle()
    {
        $table = $this->argument('table');

        // Remove DB_PREFIX from table name if it exists
        $dbPrefix = env('DB_PREFIX', '');
        if (!empty($dbPrefix) && str_starts_with($table, $dbPrefix)) {
            $table = substr($table, strlen($dbPrefix));
        }

        $modelName = Str::studly(Str::singular($table));

        // Get columns (you might need to pass the full table name with prefix here)
        $columns = $this->getColumns($this->argument('table')); // or use $table with prefix?

        if (empty($columns)) {
            $this->error("No columns found for table '{$table}'");
            return 1;
        }

        // Generate files
        $this->generateModel($modelName, $table, $columns);
        $this->generateRepository($modelName, $columns);
        $this->generateService($modelName, $columns);

        $this->info("✓ Model, Repository, and Service for '{$modelName}' created successfully!");

        return 0;
    }

    protected function getColumns($table)
    {
        // If columns are provided via option, use them
        if ($columns = $this->option('columns')) {
            return array_map('trim', explode(',', $columns));
        }

        // Otherwise, get columns from database table
        try {
            if (Schema::hasTable($table)) {
                $columns = Schema::getColumnListing($table);
                // Filter out common Laravel columns if needed
                return array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);
            }
        } catch (\Exception $e) {
            $this->warn("Could not fetch columns from database: " . $e->getMessage());
        }

        return [];
    }

    protected function generateModel($modelName, $table, $columns)
    {
        $modelPath = app_path("Models/{$modelName}.php");
        $content = $this->getModelTemplate($modelName, $table, $columns);

        $this->createFile($modelPath, $content);
        $this->info("Model created: {$modelPath}");
    }

    protected function generateRepository($modelName, $columns)
    {
        $repositoryPath = app_path("Repositories/{$modelName}Repository.php");
        $content = $this->getRepositoryTemplate($modelName, $columns);

        $this->createFile($repositoryPath, $content);
        $this->info("Repository created: {$repositoryPath}");
    }

    protected function generateService($modelName, $columns)
    {
        $servicePath = app_path("Services/{$modelName}Service.php");
        $content = $this->getServiceTemplate($modelName, $columns);

        $this->createFile($servicePath, $content);
        $this->info("Service created: {$servicePath}");
    }

    protected function createFile($path, $content)
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $content);
    }

    protected function getModelTemplate($modelName, $table, $columns)
    {
        $constants = $this->generateModelConstants($columns);
        $fillable = $this->generateFillable($columns);
        $useSoftDeletes = in_array('deleted_at', $columns) ? 'use SoftDeletes;' : '';

        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class {$modelName} extends {$this->baseModel}
{
    {$useSoftDeletes}
    protected \$table = "{$table}";
    
{$constants}
    protected \$fillable = [
{$fillable}
    ];
}
PHP;
    }

    protected function generateModelConstants($columns)
    {
        $constants = [];
        foreach ($columns as $column) {
            $constantName = strtoupper($column);
            $constants[] = "    const {$constantName} = \"{$column}\";";
        }
        return implode("\n", $constants);
    }

    protected function generateFillable($columns)
    {
        $fillable = [];
        foreach ($columns as $column) {
            $fillable[] = "        self::" . strtoupper($column) . ",";
        }
        return implode("\n", $fillable);
    }

    protected function getRepositoryTemplate($modelName, $columns)
    {
        $getterMethods = $this->generateRepositoryGetters($modelName, $columns);

        return <<<PHP
<?php

namespace App\Repositories;

use App\Models\\{$modelName};

class {$modelName}Repository extends {\$this->baseRepository}
{
    public function __construct({$modelName} \$model)
    {
        parent::__construct(\$model);
    }

    // column constants
{\$getterMethods}
    // functions
}
PHP;
    }

    protected function generateRepositoryGetters($modelName, $columns)
    {
        $methods = [];
        foreach ($columns as $column) {
            $methodName = Str::camel($column);
            $constantName = strtoupper($column);
            $methods[] = <<<PHP
    public function {\$methodName}()
    {
        return {$modelName}::{\$constantName};
    }
PHP;
        }
        return implode("\n\n", $methods);
    }

    protected function getServiceTemplate($modelName, $columns)
    {
        $getterMethods = $this->generateServiceGetters($columns);

        return <<<PHP
<?php

namespace App\Services;

use App\Repositories\\{$modelName}Repository;

/**
 * @property {$modelName}Repository \$repository
 */
class {$modelName}Service extends {\$this->baseService}
{
    protected \$repository;
    
    public function __construct({$modelName}Repository \$repository)
    {
        \$this->repository = \$repository;
    }

    // column constants
{$getterMethods}
    // functions
}
PHP;
    }

    protected function generateServiceGetters($columns)
    {
        $methods = [];
        foreach ($columns as $column) {
            $methodName = Str::camel($column);
            $methods[] = <<<PHP
    public function {$methodName}()
    {
        return \$this->repository->{$methodName}();
    }
PHP;
        }
        return implode("\n\n", $methods);
    }
}
