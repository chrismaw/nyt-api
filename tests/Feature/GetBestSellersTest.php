<?php

test('returns first 20 list of books', function () {
    $response = $this->get('/api/1/nyt/best-sellers?test=1');
    $response->assertOK()
    ->assertJsonCount(20);
});
