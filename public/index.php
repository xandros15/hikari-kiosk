<?php

declare(strict_types=1);

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Yiisoft\Cache\File\FileCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Query\Query;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;
use Yiisoft\Db\Sqlite\Dsn;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Headers: *');

require __DIR__ . '/../vendor/autoload.php';
$logger = new Logger('app', [
    new StreamHandler('php://stderr', Level::Error),
    new StreamHandler('php://stdout', Level::Info),
], [new UidProcessor(),],);
ErrorHandler::register($logger);
$cache = new SchemaCache(new FileCache(__DIR__ . '/../var/cache'));
$dbFile = __DIR__ . '/../data/db.sqlite';
$db = new Connection(new Driver((string) (new Dsn('sqlite', $dbFile))), $cache);
$query = new Query($db);
$query->select([
    'id',
    'room',
    'room_id',
    'date_start',
    'duration',
    'title',
    'description',
    'tags',
    'speakers',
    'blocks',
])->from('attraction')->andFilterWhere(['non_stop' => 0]);

$data = array_map(fn($a) => new \Hikari\Kiosk\AttractionReadModel(
    id: (string) $a['id'],
    startDateTime: \DateTimeImmutable::createFromFormat('Y-m-d H:i', $a['date_start'])->format('Y-m-d H:i:00'),
    endDateTime: \DateTimeImmutable::createFromFormat('Y-m-d H:i', $a['date_start'])->modify("+ {$a['duration']} minutes")->format('Y-m-d H:i:00'),
    title: preg_replace('/^\[\w+]\s/', '', $a['title']),
    description: $a['description'],
    speaker: array_values(array_map(fn($p) => $p['title'], json_decode($a['speakers'], true)['list'] ?? [])),
    room: $a['room'],
    roomId: $a['room_id'],
    block: json_decode($a['blocks'], true)['list'] ?? [],
    tags: json_decode($a['tags'], true),
), $query->all());


header('Content-type: application/json');
echo json_encode($data);
