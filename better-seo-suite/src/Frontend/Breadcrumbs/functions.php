<?php
namespace BSS\Frontend\Breadcrumbs;

function the_breadcrumbs(): void
{
    echo do_shortcode('[bss_breadcrumbs]');
}

