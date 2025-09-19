<?php

use App\Services\Contracts\{
    SrsServiceInterface,
    McqGeneratorServiceInterface,
    MatchingGeneratorServiceInterface
};

it('resolves SrsServiceInterface from the container', function () {
    $svc = app(SrsServiceInterface::class);
    expect($svc)->not->toBeNull();
});

it('resolves McqGeneratorServiceInterface from the container', function () {
    $svc = app(McqGeneratorServiceInterface::class);
    expect($svc)->not->toBeNull();
});

it('resolves MatchingGeneratorServiceInterface from the container', function () {
    $svc = app(MatchingGeneratorServiceInterface::class);
    expect($svc)->not->toBeNull();
});
