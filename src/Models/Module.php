<?php

namespace DonorServices\FilamentModule\Models;

use DonorServices\FilamentModule\Jobs\RescanModuleResources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Module extends Model
{
    protected $table = 'filament_module_table';

    protected static string $filament_path = 'app/Filament';

    protected $fillable = ['name', 'is_active', 'resources', 'pages'];

    protected $casts = [
        'is_active' => 'boolean',
        'resources' => 'array',
        'pages' => 'array',
    ];

    public static function scanAndUpdateModules()
    {
        $modules = File::directories(base_path('Modules'));

        foreach ($modules as $moduleDir) {
            $moduleName = basename($moduleDir);
            $module = self::firstOrCreate(['name' => $moduleName]);

            $resourcePath = $moduleDir . '/Filament/Resources';
            $pagePath = $moduleDir . '/Filament/Pages';
            $resourceHash = self::generateDirectoryHash($resourcePath);

            if ($module->resource_hash !== $resourceHash) {
                try {
                    $module->update([
                        'resources' => self::discoverClassesInDirectory($resourcePath),
                        'pages' => self::discoverClassesInDirectory($pagePath),
                        'resource_hash' => $resourceHash,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Failed to update module {$moduleName}: " . $e->getMessage());
                }
            }
        }
    }

    protected static function discoverClassesInDirectory(string $directory): array
    {
        $classes = [];
        if (! File::exists($directory)) {
            return $classes;
        }

        $files = File::files($directory);
        foreach ($files as $file) {
            $relativePath = str_replace(base_path() . '/', '', $file->getPath());
            $namespace = str_replace('/', '\\', $relativePath);
            $className = $file->getBasename('.php');
            $fullClassName = $namespace . '\\' . $className;

            if (class_exists($fullClassName)) {
                $classes[] = $fullClassName;
            }
        }

        return $classes;
    }

    protected static function generateDirectoryHash($directory): string
    {
        if (! File::exists($directory)) {
            return '';
        }

        $files = File::allFiles($directory);
        $hashData = [];

        foreach ($files as $file) {
            $hashData[] = $file->getFilename() . $file->getMTime();
        }

        sort($hashData);

        return md5(implode('', $hashData));
    }

    public static function dispatchRescanJob(): void
    {
        try {
            RescanModuleResources::dispatch();
            Log::info('RescanModuleResources job dispatched successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to dispatch RescanModuleResources job: ' . $e->getMessage());
        }
    }

    public static function activeResources()
    {
        return static::where('is_active', true)->get()
            ->flatMap(fn ($module) => $module->resources)
            ->toArray();
    }

    public static function activePages()
    {
        return static::where('is_active', true)->get()
            ->flatMap(fn ($module) => $module->pages)
            ->toArray();
    }
}
