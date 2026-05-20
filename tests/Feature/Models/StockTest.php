<?php

use App\Enum\PurchaseOrderStatus;
use App\Enum\StockMovementType;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockMovement;

test('stock category can have a parent', function () {
    $parent = StockCategory::factory()->create(['name' => 'Parent']);
    $child = StockCategory::factory()->create(['parent_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($parent->children->first()->id)->toBe($child->id);
});

test('stock item belongs to a category', function () {
    $category = StockCategory::factory()->create();
    $item = StockItem::factory()->create(['category_id' => $category->id]);

    expect($item->category->id)->toBe($category->id);
});

test('stock movement type enum casts correctly', function () {
    $movement = StockMovement::factory()->create(['type' => 'IN']);

    expect($movement->type->value)->toBe(StockMovementType::In->value);
});

test('purchase order generates po number on create', function () {
    $po = PurchaseOrder::factory()->create();

    expect($po->po_number)->toStartWith('PO/');
});

test('purchase order status enum casts correctly', function () {
    $po = PurchaseOrder::factory()->approved()->create();

    expect($po->status->value)->toBe(PurchaseOrderStatus::Approved->value);
});

test('purchase order has items relationship', function () {
    $po = PurchaseOrder::factory()->create();
    $po->items()->createMany(
        PurchaseOrderItem::factory()->count(3)->make()->toArray()
    );

    expect($po->items()->count())->toBe(3);
});
