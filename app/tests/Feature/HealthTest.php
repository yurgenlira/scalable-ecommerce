<?php

it('exposes a health endpoint', function () {
    $this->get('/up')->assertOk();
});
