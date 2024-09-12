<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Employee;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear permisos si no existen
        $permissions = [
            'pos.menu' => 'pos',
            'employee.menu' => 'employee',
            'customer.menu' => 'customer',
            'supplier.menu' => 'supplier',
            'salary.menu' => 'salary',
            'attendence.menu' => 'attendence',
            'category.menu' => 'category',
            'product.menu' => 'product',
            'orders.menu' => 'orders',
            'stock.menu' => 'stock',
            'roles.menu' => 'roles',
            'user.menu' => 'user',
            'database.menu' => 'database'
        ];

        foreach ($permissions as $name => $groupName) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ], [
                'group_name' => $groupName,
            ]);
        }

        // Crear roles
        $superAdmin = Role::firstOrCreate(['name' => 'Superadministrador']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $accountRole = Role::firstOrCreate(['name' => 'Cajero']);
        $managerRole = Role::firstOrCreate(['name' => 'Encargado']);
        $salespersonRole = Role::firstOrCreate(['name' => 'Vendedor']);

        // Asignar permisos a roles
        $superAdmin->givePermissionTo(Permission::all());
        $adminRole->givePermissionTo(['customer.menu', 'user.menu', 'supplier.menu']);
        $accountRole->givePermissionTo(['customer.menu', 'user.menu', 'supplier.menu']);
        $managerRole->givePermissionTo(['stock.menu', 'orders.menu', 'product.menu', 'salary.menu', 'employee.menu']);
        $salespersonRole->givePermissionTo(['product.menu', 'pos.menu']);

        // Crear usuarios
        $owner = User::firstOrCreate([
            'name' => 'Dueño',
            'username' => 'dueno',
            'email' => 'dueno@gmail.com',
        ], [
            'password' => Hash::make('password'), // Cambia 'password' por la contraseña deseada
        ]);

        $manager = User::firstOrCreate([
            'name' => 'Encargado',
            'username' => 'encargado',
            'email' => 'encargado@gmail.com',
        ], [
            'password' => Hash::make('password'), // Cambia 'password' por la contraseña deseada
        ]);

        $salesperson = User::firstOrCreate([
            'name' => 'Vendedor',
            'username' => 'vendedor',
            'email' => 'vendedor@gmail.com',
        ], [
            'password' => Hash::make('password'), // Cambia 'password' por la contraseña deseada
        ]);

        // Asignar roles a los usuarios
        $owner->assignRole('Superadministrador'); // Dueño
        $manager->assignRole('Encargado');         // Encargado
        $salesperson->assignRole('Vendedor');      // Vendedor

        // Crear otros datos de prueba
        Category::factory(5)->create();
        Employee::factory(5)->create();
        // AdvanceSalary::factory(25)->create();
        // Customer::factory(25)->create();
        // Supplier::factory(10)->create();
    }
}