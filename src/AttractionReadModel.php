<?php

declare(strict_types=1);

namespace Hikari\Kiosk;

final readonly class AttractionReadModel implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $day,
        public string $start,
        public string $end,
        public string $title,
        public string $description,
        public string $speaker,
        public string $room,
        public string $roomPosition,
        public string $block,
        public string $type,
        public string $photoUrl,
        public string $icon,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'start' => $this->start,
            'end' => $this->end,
            'title' => $this->title,
            'description' => $this->description,
            'speaker' => $this->speaker,
            'room' => $this->room,
            'room_position' => $this->roomPosition,
            'block' => $this->block,
            'type' => $this->type,
            'photo_url' => $this->photoUrl,
            'icon' => $this->icon,
        ];
    }
}
