<?php

use App\Models\Category;

beforeEach(function () {
    $this->parentCategory = Category::factory()->create([
        'name' => 'Éclairage',
        'slug' => 'eclairage',
        'is_visible' => true,
    ]);

    $this->childCategory = Category::factory()->create([
        'name' => 'LED Intérieur',
        'slug' => 'led-interieur',
        'parent_id' => $this->parentCategory->id,
        'is_visible' => true,
    ]);

    $this->hiddenCategory = Category::factory()->create([
        'is_visible' => false,
    ]);
});

test('can list categories', function () {
    $response = $this->getJson('/api/v1/categories');

    $response->assertOk()
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'slug',
                'parentId',
                'isVisible',
                'sortOrder',
            ]
        ]);

    expect($response->json())->toHaveCount(2); // Only visible categories
});

test('only shows visible categories by default', function () {
    $response = $this->getJson('/api/v1/categories');

    $response->assertOk();
    
    $categoryIds = collect($response->json())->pluck('id');
    expect($categoryIds)->not->toContain($this->hiddenCategory->id);
});

test('can filter categories by visibility', function () {
    $response = $this->getJson('/api/v1/categories?visible=0');

    $response->assertOk();
    
    $categoryIds = collect($response->json())->pluck('id');
    expect($categoryIds)->toContain($this->hiddenCategory->id);
});

test('can filter categories by parent', function () {
    $response = $this->getJson("/api/v1/categories?parent={$this->parentCategory->id}");

    $response->assertOk();
    expect($response->json())->toHaveCount(1);
    expect($response->json()[0]['id'])->toBe($this->childCategory->id);
});

test('can filter categories by parent slug', function () {
    $response = $this->getJson("/api/v1/categories?parent={$this->parentCategory->slug}");

    $response->assertOk();
    expect($response->json())->toHaveCount(1);
    expect($response->json()[0]['id'])->toBe($this->childCategory->id);
});

test('can show specific category', function () {
    $response = $this->getJson("/api/v1/categories/{$this->parentCategory->slug}");

    $response->assertOk()
        ->assertJsonStructure([
            'id',
            'name',
            'slug',
            'parentId',
            'isVisible',
            'sortOrder',
            'children',
        ]);

    expect($response->json('id'))->toBe($this->parentCategory->id);
    expect($response->json('children'))->toHaveCount(1);
});

test('returns 404 for non-existent category', function () {
    $response = $this->getJson('/api/v1/categories/non-existent-slug');

    $response->assertNotFound();
});

test('categories are ordered by sort_order and name', function () {
    Category::factory()->create(['name' => 'Z Category', 'sort_order' => 1, 'is_visible' => true]);
    Category::factory()->create(['name' => 'A Category', 'sort_order' => 2, 'is_visible' => true]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertOk();
    
    $names = collect($response->json())->pluck('name');
    expect($names->first())->toBe('Z Category'); // Lower sort_order comes first
});