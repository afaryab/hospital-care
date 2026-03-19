<?php

test('admin panel login page is reachable', function () {
    $this->get('/admin/login')->assertStatus(200);
});

test('accounts panel login page is reachable', function () {
    $this->get('/accounts/login')->assertStatus(200);
});
