<?php

namespace RectorPrefix202507\Illuminate\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}
