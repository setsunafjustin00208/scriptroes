<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class LibraryManagement extends BaseCommand
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
    protected $name = 'library:manage';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Create, list, or delete CodeIgniter4 libraries.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'library:manage [create|list|delete] [LibraryName]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'action' => 'Action to perform: create, list, or delete',
        'name'   => 'Name of the library (for create/delete)',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $action = $params[0] ?? null;
        $name = $params[1] ?? null;
        $librariesPath = APPPATH . 'Libraries/';

        switch ($action) {
            case 'create':
                if (!$name) {
                    CLI::error('Please provide a library name.');
                    return;
                }
                $file = $librariesPath . $name . '.php';
                if (file_exists($file)) {
                    CLI::error("Library '$name' already exists.");
                    return;
                }
                $template = "<?php\n\nnamespace App\\Libraries;\n\nclass $name\n{\n    // Add your library methods here\n}\n";
                if (file_put_contents($file, $template) !== false) {
                    CLI::write("Library '$name' created at app/Libraries/$name.php", 'green');
                } else {
                    CLI::error('Failed to create library.');
                }
                break;
            case 'list':
                $files = glob($librariesPath . '*.php');
                if (!$files) {
                    CLI::write('No libraries found.');
                    return;
                }
                CLI::write('Libraries:');
                foreach ($files as $file) {
                    CLI::write('- ' . basename($file, '.php'));
                }
                break;
            case 'delete':
                if (!$name) {
                    CLI::error('Please provide a library name.');
                    return;
                }
                $file = $librariesPath . $name . '.php';
                if (!file_exists($file)) {
                    CLI::error("Library '$name' does not exist.");
                    return;
                }
                if (unlink($file)) {
                    CLI::write("Library '$name' deleted.", 'yellow');
                } else {
                    CLI::error('Failed to delete library.');
                }
                break;
            default:
                CLI::write('Usage: ' . $this->usage);
                break;
        }
    }
}
