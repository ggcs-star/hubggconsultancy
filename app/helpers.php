<?php

if (! function_exists('status_badge_classes')) {
    function status_badge_classes(bool $active): string
    {
        return $active
            ? 'bg-success-light text-success'
            : 'bg-secondary-light text-secondary-dark';
    }
}
