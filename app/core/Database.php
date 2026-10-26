<?php
class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $dir = dirname(DB_PATH);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new RuntimeException('Não foi possível criar a pasta do banco SQLite.');
            }

            self::$connection = new PDO('sqlite:' . DB_PATH, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            self::$connection->exec('PRAGMA foreign_keys = ON');
            self::initialize(self::$connection);
        }
        return self::$connection;
    }

    private static function initialize(PDO $db): void
    {
        $db->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    company TEXT NULL,
    role TEXT NULL DEFAULT 'Usuário',
    avatar TEXT NULL,
    theme TEXT NOT NULL DEFAULT 'light',
    notify_device INTEGER NOT NULL DEFAULT 1,
    notify_email INTEGER NOT NULL DEFAULT 0,
    profile_visible INTEGER NOT NULL DEFAULT 1,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    phone TEXT NOT NULL,
    email TEXT NULL,
    whatsapp TEXT NULL,
    telegram TEXT NULL,
    teams TEXT NULL,
    address TEXT NULL,
    notes TEXT NULL,
    status TEXT NOT NULL DEFAULT 'Ativo',
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_contacts_name ON contacts(name);
CREATE INDEX IF NOT EXISTS idx_contacts_user ON contacts(user_id);

CREATE TABLE IF NOT EXISTS events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT NULL,
    event_date TEXT NOT NULL,
    start_time TEXT NOT NULL,
    end_time TEXT NULL,
    location TEXT NULL,
    category TEXT NOT NULL DEFAULT 'Trabalho',
    color TEXT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_events_user_date ON events(user_id, event_date);

CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT NULL,
    priority TEXT NOT NULL DEFAULT 'media',
    status TEXT NOT NULL DEFAULT 'pendente',
    due_date TEXT NULL,
    due_time TEXT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_tasks_user ON tasks(user_id);

CREATE TABLE IF NOT EXISTS groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    description TEXT NULL,
    tag TEXT NULL,
    color TEXT NULL DEFAULT '#0D9488',
    icon TEXT NULL DEFAULT 'users',
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS group_members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL,
    contact_id INTEGER NOT NULL,
    joined_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    UNIQUE(group_id, contact_id)
);

CREATE TABLE IF NOT EXISTS group_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    body TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
SQL);

        // Migração suave para bancos já existentes
        self::ensureColumn($db, 'users', 'company', 'TEXT NULL');
        self::ensureColumn($db, 'users', 'role', "TEXT NULL DEFAULT 'Usuário'");
        self::ensureColumn($db, 'users', 'avatar', 'TEXT NULL');
        self::ensureColumn($db, 'users', 'theme', "TEXT NOT NULL DEFAULT 'light'");
        self::ensureColumn($db, 'users', 'notify_device', 'INTEGER NOT NULL DEFAULT 1');
        self::ensureColumn($db, 'users', 'notify_email', 'INTEGER NOT NULL DEFAULT 0');
        self::ensureColumn($db, 'users', 'profile_visible', 'INTEGER NOT NULL DEFAULT 1');
        self::ensureColumn($db, 'contacts', 'telegram', 'TEXT NULL');
        self::ensureColumn($db, 'contacts', 'teams', 'TEXT NULL');
        self::ensureColumn($db, 'contacts', 'status', "TEXT NOT NULL DEFAULT 'Ativo'");
    }

    private static function ensureColumn(PDO $db, string $table, string $column, string $definition): void
    {
        $cols = $db->query("PRAGMA table_info($table)")->fetchAll();
        foreach ($cols as $col) {
            if ($col['name'] === $column) {
                return;
            }
        }
        $db->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    }
}
