<?php

namespace Database\Seeders;

use App\Enums\ColumnType;
use App\Enums\IndexType;
use App\Models\PivotRelationship;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\TableColumn;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'E-commerce Demo',
            'description' => 'A demo e-commerce database schema.',
        ]);

        // Users table
        $usersTable = ProjectTable::create(['project_id' => $project->id, 'name' => 'users']);
        TableColumn::create(['project_table_id' => $usersTable->id, 'name' => 'id', 'type' => ColumnType::BigInteger, 'is_primary' => true, 'is_unsigned' => true, 'position' => 0]);
        TableColumn::create(['project_table_id' => $usersTable->id, 'name' => 'name', 'type' => ColumnType::String, 'length' => 255, 'position' => 1]);
        TableColumn::create(['project_table_id' => $usersTable->id, 'name' => 'email', 'type' => ColumnType::String, 'length' => 255, 'index_type' => IndexType::Unique, 'position' => 2]);
        TableColumn::create(['project_table_id' => $usersTable->id, 'name' => 'created_at', 'type' => ColumnType::Timestamp, 'is_nullable' => true, 'position' => 3]);
        TableColumn::create(['project_table_id' => $usersTable->id, 'name' => 'updated_at', 'type' => ColumnType::Timestamp, 'is_nullable' => true, 'position' => 4]);

        // Products table
        $productsTable = ProjectTable::create(['project_id' => $project->id, 'name' => 'products']);
        TableColumn::create(['project_table_id' => $productsTable->id, 'name' => 'id', 'type' => ColumnType::BigInteger, 'is_primary' => true, 'is_unsigned' => true, 'position' => 0]);
        TableColumn::create(['project_table_id' => $productsTable->id, 'name' => 'name', 'type' => ColumnType::String, 'length' => 255, 'position' => 1]);
        TableColumn::create(['project_table_id' => $productsTable->id, 'name' => 'price', 'type' => ColumnType::Decimal, 'position' => 2]);
        TableColumn::create(['project_table_id' => $productsTable->id, 'name' => 'created_at', 'type' => ColumnType::Timestamp, 'is_nullable' => true, 'position' => 3]);
        TableColumn::create(['project_table_id' => $productsTable->id, 'name' => 'updated_at', 'type' => ColumnType::Timestamp, 'is_nullable' => true, 'position' => 4]);

        // Orders table
        $ordersTable = ProjectTable::create(['project_id' => $project->id, 'name' => 'orders']);
        TableColumn::create(['project_table_id' => $ordersTable->id, 'name' => 'id', 'type' => ColumnType::BigInteger, 'is_primary' => true, 'is_unsigned' => true, 'position' => 0]);
        TableColumn::create(['project_table_id' => $ordersTable->id, 'name' => 'user_id', 'type' => ColumnType::BigInteger, 'is_unsigned' => true, 'fk_table' => 'users', 'fk_column' => 'id', 'position' => 1]);
        TableColumn::create(['project_table_id' => $ordersTable->id, 'name' => 'total_amount', 'type' => ColumnType::Decimal, 'position' => 2]);
        TableColumn::create(['project_table_id' => $ordersTable->id, 'name' => 'status', 'type' => ColumnType::String, 'length' => 50, 'default_value' => 'pending', 'position' => 3]);
        TableColumn::create(['project_table_id' => $ordersTable->id, 'name' => 'created_at', 'type' => ColumnType::Timestamp, 'is_nullable' => true, 'position' => 4]);
        TableColumn::create(['project_table_id' => $ordersTable->id, 'name' => 'updated_at', 'type' => ColumnType::Timestamp, 'is_nullable' => true, 'position' => 5]);

        // Pivot: order_product
        PivotRelationship::create([
            'project_id' => $project->id,
            'table_one_id' => $ordersTable->id,
            'table_two_id' => $productsTable->id,
            'pivot_table_name' => 'order_product',
            'with_timestamps' => true,
        ]);
    }
}
