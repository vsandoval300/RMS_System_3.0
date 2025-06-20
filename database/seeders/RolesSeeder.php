<?php

namespace Database\Seeders;
use App\Models\roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    
    {
        //
        $roles=new roles();              $roles->name = 'admin';             $roles->guard_name = 'web';                   $roles->created_at = date('Y-m-d',strtotime('2025-1-3'));                $roles->updated_at = date('Y-m-d',strtotime('2025-1-3'));                 $roles->save(); 
        $roles=new roles();              $roles->name = 'staff';             $roles->guard_name = 'web';                   $roles->created_at = date('Y-m-d',strtotime('2025-1-3'));                $roles->updated_at = date('Y-m-d',strtotime('2025-1-3'));                 $roles->save(); 
        $roles=new roles();              $roles->name = 'user';             $roles->guard_name = 'web';                   $roles->created_at = date('Y-m-d',strtotime('2025-1-3'));                $roles->updated_at = date('Y-m-d',strtotime('2025-1-3'));                 $roles->save(); 
        $roles=new roles();              $roles->name = 'Test';             $roles->guard_name = 'web';                   $roles->created_at = date('Y-m-d',strtotime('2025-1-9'));                $roles->updated_at = date('Y-m-d',strtotime('2025-1-9'));                 $roles->save(); 
        $roles=new roles();              $roles->name = 'super-admin';             $roles->guard_name = 'web';                   $roles->created_at = date('Y-m-d',strtotime('2025-1-3'));                $roles->updated_at = date('Y-m-d',strtotime('2025-1-3'));                 $roles->save();  
    }
}
