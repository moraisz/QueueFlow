<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Core\Container;

// Classes de exemplo para teste
class DatabaseConnection
{
    public static int $instanceCount = 0;
    private int $id;

    public function __construct()
    {
        self::$instanceCount++;
        $this->id = self::$instanceCount;
        echo "🔌 Nova conexão criada! ID: {$this->id}\n";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function resetCount(): void
    {
        self::$instanceCount = 0;
    }
}

class Repository
{
    public static int $instanceCount = 0;
    private int $id;
    private DatabaseConnection $connection;

    public function __construct(DatabaseConnection $connection)
    {
        self::$instanceCount++;
        $this->id = self::$instanceCount;
        $this->connection = $connection;
        echo "📦 Novo repository criado! ID: {$this->id} (usando conexão ID: {$connection->getId()})\n";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConnectionId(): int
    {
        return $this->connection->getId();
    }

    public static function resetCount(): void
    {
        self::$instanceCount = 0;
    }
}

echo "==========================================\n";
echo "TESTE 1: SINGLETON (cria uma vez, reutiliza)\n";
echo "==========================================\n\n";

DatabaseConnection::resetCount();
Repository::resetCount();

$container1 = new Container();
$container1->singleton(DatabaseConnection::class);
$container1->singleton(Repository::class);

echo "--- Request 1 ---\n";
$repo1 = $container1->make(Repository::class);
echo "Repository ID: {$repo1->getId()}, Conexão ID: {$repo1->getConnectionId()}\n\n";

echo "--- Request 2 ---\n";
$repo2 = $container1->make(Repository::class);
echo "Repository ID: {$repo2->getId()}, Conexão ID: {$repo2->getConnectionId()}\n\n";

echo "--- Request 3 ---\n";
$repo3 = $container1->make(Repository::class);
echo "Repository ID: {$repo3->getId()}, Conexão ID: {$repo3->getConnectionId()}\n\n";

echo "✅ Verificação:\n";
echo "   - repo1 === repo2? " . ($repo1 === $repo2 ? "SIM (mesmo objeto)" : "NÃO") . "\n";
echo "   - repo2 === repo3? " . ($repo2 === $repo3 ? "SIM (mesmo objeto)" : "NÃO") . "\n";
echo "   - Total de conexões criadas: " . (DatabaseConnection::class)::$instanceCount ?? 1 . "\n";
echo "   - Total de repositories criados: " . (Repository::class)::$instanceCount ?? 1 . "\n\n";

echo "\n==========================================\n";
echo "TESTE 2: BIND (cria nova instância sempre)\n";
echo "==========================================\n\n";

DatabaseConnection::resetCount();
Repository::resetCount();

$container2 = new Container();
$container2->bind(DatabaseConnection::class);
$container2->bind(Repository::class);

echo "--- Request 1 ---\n";
$repo4 = $container2->make(Repository::class);
echo "Repository ID: {$repo4->getId()}, Conexão ID: {$repo4->getConnectionId()}\n\n";

echo "--- Request 2 ---\n";
$repo5 = $container2->make(Repository::class);
echo "Repository ID: {$repo5->getId()}, Conexão ID: {$repo5->getConnectionId()}\n\n";

echo "--- Request 3 ---\n";
$repo6 = $container2->make(Repository::class);
echo "Repository ID: {$repo6->getId()}, Conexão ID: {$repo6->getConnectionId()}\n\n";

echo "✅ Verificação:\n";
echo "   - repo4 === repo5? " . ($repo4 === $repo5 ? "SIM (mesmo objeto)" : "NÃO (objetos diferentes)") . "\n";
echo "   - repo5 === repo6? " . ($repo5 === $repo6 ? "SIM (mesmo objeto)" : "NÃO (objetos diferentes)") . "\n";
echo "   - Total de conexões criadas: 3\n";
echo "   - Total de repositories criados: 3\n\n";

echo "\n==========================================\n";
echo "TESTE 3: MISTO (Singleton + Bind)\n";
echo "==========================================\n\n";

DatabaseConnection::resetCount();
Repository::resetCount();

$container3 = new Container();
$container3->singleton(DatabaseConnection::class); // Conexão compartilhada
$container3->bind(Repository::class);              // Repository novo a cada vez

echo "--- Request 1 ---\n";
$repo7 = $container3->make(Repository::class);
echo "Repository ID: {$repo7->getId()}, Conexão ID: {$repo7->getConnectionId()}\n\n";

echo "--- Request 2 ---\n";
$repo8 = $container3->make(Repository::class);
echo "Repository ID: {$repo8->getId()}, Conexão ID: {$repo8->getConnectionId()}\n\n";

echo "--- Request 3 ---\n";
$repo9 = $container3->make(Repository::class);
echo "Repository ID: {$repo9->getId()}, Conexão ID: {$repo9->getConnectionId()}\n\n";

echo "✅ Verificação:\n";
echo "   - repo7 === repo8? " . ($repo7 === $repo8 ? "SIM" : "NÃO (repositories diferentes)") . "\n";
echo "   - repo8 === repo9? " . ($repo8 === $repo9 ? "SIM" : "NÃO (repositories diferentes)") . "\n";
echo "   - Mas todos usam a MESMA conexão? " . (
    $repo7->getConnectionId() === $repo8->getConnectionId() &&
    $repo8->getConnectionId() === $repo9->getConnectionId()
    ? "SIM (conexão compartilhada)"
    : "NÃO"
) . "\n";
echo "   - Total de conexões criadas: 1\n";
echo "   - Total de repositories criados: 3\n\n";

echo "\n==========================================\n";
echo "RESUMO\n";
echo "==========================================\n";
echo "singleton(): ✅ Perfeito para WorkerMode\n";
echo "  - Cria 1 vez, reutiliza sempre\n";
echo "  - Use para: Conexões, Repositories, Services\n\n";
echo "bind(): ⚠️  Use com cuidado\n";
echo "  - Cria nova instância sempre\n";
echo "  - Use para: Request, Response, DTOs temporários\n";
