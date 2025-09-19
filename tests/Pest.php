<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// boot Laravel + chạy migrate trước mỗi test
uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');
