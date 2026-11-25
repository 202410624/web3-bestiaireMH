<?php

namespace App\Models;

class TeamModel
{
    protected array $members = [
        ['image' => 'people-1.jpg', 'name' => 'Ryan White', 'role' => 'Directeur général', 'bio' => 'Mauris ante tellus, feugiat nec metus non, bibendum semper velit. Praesent laoreet urna id tristique fermentum.'],
        ['image' => 'people-2.jpg', 'name' => 'Catherine Pinky', 'role' => 'Directrice marketing', 'bio' => 'Sed faucibus nec velit finibus accumsan. Sed varius augue et leo pharetra, in varius lacus eleifend.'],
        ['image' => 'people-3.jpg', 'name' => 'Johnny Brief', 'role' => 'Responsable comptabilité', 'bio' => 'Sed faucibus nec velit finibus accumsan. Sed varius augue et leo pharetra, in varius lacus eleifend.'],
        ['image' => 'people-4.jpg', 'name' => 'George Nelson', 'role' => 'Directeur artistique', 'bio' => 'Nunc convallis facilisis congue. Curabitur gravida rutrum justo sed pulvinar. Pellentesque ac ante in erat bibendum dignissim.'],
    ];

    public function all(): array
    {
        return $this->members;
    }
}