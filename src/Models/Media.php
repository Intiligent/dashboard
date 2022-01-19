<?php

namespace Dashboard\Models;

use App\Models\Media as MediaBase;

class Media extends MediaBase
{
    protected $appends = ['basename', 'humansize', 'dimension', 'icon'];
}
