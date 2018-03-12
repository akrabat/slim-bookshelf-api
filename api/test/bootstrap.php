<?php
namespace AppTest;

require __DIR__ . '/../vendor/autoload.php';


class Bootstrap
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * Fetch the configured container
     *
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        if (null === self::$container) {
            // Use an in-memory database when testing
            putenv('DB_DSN=sqlite::memory:');
            $settings = include __DIR__ . '/../src/settings.php';

            $container = new \Slim\Container(['settings' => $settings]);
            include __dir__ . '/../src/dependencies.php';

            self::$container = $container;
        }

        return self::$container;
    }

    /**
     * Run migrations to set up database
     */
    public static function initialiseDatabase()
    {
        $container = self::getContainer();
        $pdo = $container->get('db');

        // Delete all tables first if there are any
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = [];
        while ($row = $stmt->fetch()) {
            $tables[] = $row['name'];
        }

        if (count($tables)) {
            $pdo->exec('PRAGMA foreign_keys = OFF');
            foreach ($tables as $table) {
                $pdo->exec('DROP TABLE IF EXISTS '. $table);
            }
            $pdo->exec('PRAGMA foreign_keys = ON');
        }

        // Now run migrations
        $migrationsConfig = [
            'directory' => realpath(__DIR__ . '/../db/migrations/'),
            'name' => 'Doctrine Database Migrations',
            'namespace' => 'Migrations',
            'table' => 'migrations',
        ];
        $driver = new \Doctrine\DBAL\Driver\PDOSqlite\Driver();
        $connection = new \Doctrine\DBAL\Connection([
            'pdo' => $pdo,
        ], $driver);

        $configuration = new \Doctrine\DBAL\Migrations\Configuration\Configuration($connection);

        $configuration->setName($migrationsConfig['name']);
        $configuration->setMigrationsDirectory($migrationsConfig['directory']);
        $configuration->setMigrationsNamespace($migrationsConfig['namespace']);
        $configuration->setMigrationsTableName($migrationsConfig['table']);
        $configuration->registerMigrationsFromDirectory($migrationsConfig['directory']);

        $migrate = new \Doctrine\DBAL\Migrations\Migration($configuration);
        $migrate->migrate();
    }

    /**
     * Execute All SQL statements in a file
     *
     * @param  String $fixtureFile Filename of file file containing SQL statements
     */
    public static function seedDatabase($fixtureFile = null)
    {
        if (strpos($fixtureFile, '/') === false) {
            $fixtureFile = __DIR__ . '/../db/fixtures/' . $fixtureFile;
        }

        if (!file_exists($fixtureFile) || !is_readable($fixtureFile)) {
            throw new \RuntimeException('Fixture file does not exist or not readable: ' . $fixtureFile);
        }
        $fixtureFile = realpath($fixtureFile);

        $pdo = self::getContainer()->get('db');

        $sql = file_get_contents($fixtureFile);
        $statements = explode(';', $sql);

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '') {
                continue;
            }

            try {
                $pdo->exec($statement);
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf(
                    "Failed to execute statement in fixture\nFile: %s\nSQL: %s\nError: %s",
                    $fixtureFile,
                    $statement,
                    $e->getMessage()
                ), 0, $e);
            }
        }
    }
}

Bootstrap::initialiseDatabase();
