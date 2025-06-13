<?php

it('has translationquery page', function () {
    $response = $this->get('/translationquery');

    $response->assertStatus(200);
});
