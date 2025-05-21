<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ViewsCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'views';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Manage view files: create, list, delete.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'views [create|list|delete] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'action' => 'The action to perform: create, list, delete',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--name' => 'View name (required for create/delete)',
        '--type' => 'View type: components, partials, pages, layout (required for create)',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $action = $params[0] ?? null;
        $viewBase = APPPATH . 'Views/';
        $scssBase = ROOTPATH . 'resources/scss/';
        $jsBase = ROOTPATH . 'resources/js/';
        switch ($action) {
            case 'create':
                $name = CLI::getOption('name') ?? CLI::prompt('View name');
                $type = CLI::getOption('type') ?? CLI::prompt('View type (components, partials, pages, layout)');
                $viewDir = $viewBase . $type . '/';
                if (!is_dir($viewDir)) mkdir($viewDir, 0777, true);
                $viewFile = $viewDir . $name . '.php';
                if (file_exists($viewFile)) {
                    CLI::error('View already exists: ' . $viewFile);
                    return;
                }
                file_put_contents($viewFile, "<!-- $type: $name -->\n");
                // Create SCSS in resources/scss/{type}/{name}.scss
                $scssTypeDir = $scssBase . $type . '/';
                if (!is_dir($scssTypeDir)) mkdir($scssTypeDir, 0777, true);
                $scssFile = $scssTypeDir . $name . '.scss';
                file_put_contents($scssFile, "/* $type/$name */\n");
                // Create JS in resources/js/{type}/{name}.js (not for layout)
                if ($type !== 'layout') {
                    $jsTypeDir = $jsBase . $type . '/';
                    if (!is_dir($jsTypeDir)) mkdir($jsTypeDir, 0777, true);
                    $jsFile = $jsTypeDir . $name . '.js';
                    file_put_contents($jsFile, "// $type/$name\n");
                }
                CLI::write("Created view: $viewFile");
                CLI::write("Created SCSS: $scssFile");
                if ($type !== 'layout') CLI::write("Created JS: $jsFile");
                break;
            case 'list':
                CLI::write('Available Views:');
                foreach (['components', 'partials', 'pages', 'layout'] as $type) {
                    $dir = $viewBase . $type . '/';
                    if (is_dir($dir)) {
                        $files = glob($dir . '*.php');
                        foreach ($files as $file) {
                            $name = basename($file, '.php');
                            CLI::write("- $type/$name");
                        }
                    }
                }
                break;
            case 'delete':
                $name = CLI::getOption('name') ?? CLI::prompt('View name');
                $type = CLI::getOption('type') ?? CLI::prompt('View type (components, partials, pages, layout)');
                $viewFile = $viewBase . $type . '/' . $name . '.php';
                $scssFile = $scssBase . $type . '/' . $name . '.scss';
                $jsFile = $jsBase . $type . '/' . $name . '.js';
                $deleted = false;
                if (file_exists($viewFile)) {
                    unlink($viewFile);
                    CLI::write('Deleted view: ' . $viewFile);
                    $deleted = true;
                }
                if (file_exists($scssFile)) {
                    unlink($scssFile);
                    CLI::write('Deleted SCSS: ' . $scssFile);
                    $deleted = true;
                }
                if ($type !== 'layout' && file_exists($jsFile)) {
                    unlink($jsFile);
                    CLI::write('Deleted JS: ' . $jsFile);
                    $deleted = true;
                }
                if (!$deleted) {
                    CLI::error('No files found for the given view/type.');
                }
                break;
            default:
                CLI::write('Usage: ' . $this->usage);
                break;
        }
    }
}
