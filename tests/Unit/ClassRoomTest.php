<?php

namespace Tests\Unit;

use App\Models\ClassRoom;
use PHPUnit\Framework\TestCase;

class ClassRoomTest extends TestCase
{
    public function test_full_name_attribute_returns_name_when_no_major(): void
    {
        $class = new ClassRoom([
            'name' => '7A',
            'major' => null,
        ]);

        $this->assertEquals('7A', $class->full_name);
    }

    public function test_full_name_attribute_appends_major_when_distinct(): void
    {
        $class = new ClassRoom([
            'name' => '10',
            'major' => 'TKJ 1',
        ]);

        $this->assertEquals('10 TKJ 1', $class->full_name);
    }

    public function test_full_name_attribute_returns_name_when_major_already_included(): void
    {
        $class = new ClassRoom([
            'name' => 'XII IPA 1',
            'major' => 'IPA',
        ]);

        $this->assertEquals('XII IPA 1', $class->full_name);
    }
}
