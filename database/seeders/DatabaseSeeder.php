<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\AdvanceSalary;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
            ]
        );

        // Usuario User
        $user = User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User',
                'username' => 'user',

            ]
        );

        // Roles y permisos
        Permission::createOrFirst(['name' => 'pos.menu', 'group_name' => 'pos']);
        Permission::createOrFirst(['name' => 'budgets.menu', 'group_name' => 'budgets']);
        Permission::createOrFirst(['name' => 'employee.menu', 'group_name' => 'employee']);
        Permission::createOrFirst(['name' => 'customer.menu', 'group_name' => 'customer']);
        Permission::createOrFirst(['name' => 'supplier.menu', 'group_name' => 'supplier']);
        Permission::createOrFirst(['name' => 'salary.menu', 'group_name' => 'salary']);
        Permission::createOrFirst(['name' => 'attendence.menu', 'group_name' => 'attendence']);
        Permission::createOrFirst(['name' => 'category.menu', 'group_name' => 'category']);
        Permission::createOrFirst(['name' => 'brand.menu', 'group_name' => 'brand']);
        Permission::createOrFirst(['name' => 'product.menu', 'group_name' => 'product']);
        Permission::createOrFirst(['name' => 'orders.menu', 'group_name' => 'orders']);
        Permission::createOrFirst(['name' => 'loans.menu', 'group_name' => 'orders']);
        Permission::createOrFirst(['name' => 'stock.menu', 'group_name' => 'stock']);
        Permission::createOrFirst(['name' => 'roles.menu', 'group_name' => 'roles']);
        Permission::createOrFirst(['name' => 'user.menu', 'group_name' => 'user']);
        Permission::createOrFirst(['name' => 'database.menu', 'group_name' => 'database']);

        Role::firstOrCreate(['name' => 'SuperAdmin'])->givePermissionTo(Permission::all());
        Role::firstOrCreate(['name' => 'Admin'])->givePermissionTo(['customer.menu', 'user.menu', 'supplier.menu']);
        Role::firstOrCreate(['name' => 'Account'])->givePermissionTo(['customer.menu', 'user.menu', 'supplier.menu']);
        Role::firstOrCreate(['name' => 'Manager'])->givePermissionTo(['stock.menu', 'orders.menu', 'product.menu', 'salary.menu', 'employee.menu']);

        $admin->assignRole('SuperAdmin');
        $user->assignRole('Account');
    }
}
