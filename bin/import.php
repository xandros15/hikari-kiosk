<?php

declare(strict_types=1);

use Yiisoft\Cache\File\FileCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;
use Yiisoft\Db\Sqlite\Dsn;

require __DIR__ . '/../vendor/autoload.php';

const IMPORT_FILE = __DIR__ . '/../data/planner-items-search.json';

if (!file_exists(IMPORT_FILE)) {
    echo IMPORT_FILE . ' not found' . PHP_EOL;
    die();
}

$json = json_decode(file_get_contents(IMPORT_FILE), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg() . PHP_EOL;
    die();
}

$cache = new SchemaCache(new FileCache(__DIR__ . '/../var/cache'));
$dbFile = __DIR__ . '/../data/db.sqlite';
$db = new Connection(new Driver((string) (new Dsn("sqlite", $dbFile))), $cache);
$db->createCommand()->setSql(
    <<<SQL
CREATE TABLE IF NOT EXISTS attraction (
    id            INTEGER PRIMARY KEY,
    room_id       INTEGER,
    room          TEXT,
    date_start    TEXT,
    duration      INTEGER,
    date          TEXT,
    `range`       TEXT,
    title         TEXT,
    description   TEXT,
    in_english    BOOLEAN,
    language_agnostic BOOLEAN,
    non_stop      BOOLEAN,
    hearing       BOOLEAN,
    level         TEXT,
    tags          TEXT,
    speakers      TEXT,
    blocks        TEXT,
    styles        TEXT
);
SQL
)->execute();

$transaction = $db->beginTransaction();
$db->createCommand()->delete('attraction')->execute();

//@todo remove after api will be fixed
function normalizeRoomName(string $roomName): string
{
    if (preg_match("/^\[(\w+)]/", $roomName, $m) && str_ends_with($roomName, " {$m[1]}")) {
        return mb_substr($roomName, 0, -(mb_strlen(" {$m[1]}")));
    }

    return $roomName;
}

$id = 0;
$data = array_map(function ($r) use (&$id) {
    return [
        'id' => ++$id,
        'room_id' => $r['room_id'],
        'room' => normalizeRoomName($r['room']),
        'blocks' => json_encode($r['blocks']),
        'date_start' => $r['date_start'],
        'duration' => $r['duration'],
        'date' => $r['date'],
        'range' => $r['range'],
        'title' => html_entity_decode($r['title']),
        'description' => html_entity_decode($r['description']),
        'speakers' => html_entity_decode(json_encode($r['speakers'])),
        'in_english' => $r['in_english'],
        'language_agnostic' => $r['language_agnostic'],
        'styles' => json_encode($r['styles']),
        'non_stop' => $r['non_stop'],
        'hearing' => $r['hearing'],
        'level' => $r['level'],
        'tags' => $r['tags'] === false ? "[]" : json_encode($r['tags']),
    ];
}, $json['data']);
$db->createCommand()->batchInsert('attraction', array_keys($data[0]), $data)->execute();
$transaction->commit();
