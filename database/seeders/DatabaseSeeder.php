<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         $user =User::factory()->create([
             'name' => 'Admin',
             'email' => 'admin@example.com',
         ]);

         Role::insert([['name' => 'admin', 'guard_name' => 'web'],['name'=> 'user', 'guard_name' => 'web']]);
         $user->assignRole('admin');

         $permission_data = [
             ['name' => 'create-category', 'guard_name'=>'web'],
             ['name' => 'update-category', 'guard_name'=>'web'],
             ['name' => 'view-category', 'guard_name'=>'web'],
             ['name' => 'delete-category', 'guard_name'=>'web'],
             ['name' => 'viewany-category', 'guard_name'=>'web'],
             ['name' => 'create-transaction', 'guard_name'=>'web'],
             ['name' => 'update-transaction', 'guard_name'=>'web'],
             ['name' => 'view-transaction', 'guard_name'=>'web'],
             ['name' => 'delete-transaction', 'guard_name'=>'web'],
             ['name' => 'viewany-transaction', 'guard_name'=>'web'],
         ];

         Permission::insert($permission_data);

         $role = Role::where('name', 'user')->first();

         $permissions_list = collect($permission_data)->map(function ($permission) {
             return $permission['name'];
         });

         $role->syncPermissions($permissions_list);

         Category::insert([
             ['name' => 'Basic Income', 'is_expenses' => false, 'image' => 'wallet.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Passive Income', 'is_expenses' => false, 'image' => 'folder.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Side Hustle', 'is_expenses' => false, 'image' => 'html.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Food & Drinks', 'is_expenses' => true, 'image' => 'meat.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Transportation', 'is_expenses' => true, 'image' => 'car.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Home & Property', 'is_expenses' => true, 'image' => 'building.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Investment', 'is_expenses' => true, 'image' => 'coin-stack.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Shopping', 'is_expenses' => true, 'image' => 'shopping-cart.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Entertaiment', 'is_expenses' => true, 'image' => 'tv-set.png', 'is_default' => true, 'created_by' => $user->id],
             ['name' => 'Loan', 'is_expenses' => true, 'image' => 'banknote.png', 'is_default' => true, 'created_by' => $user->id],
         ]);

    }
}
