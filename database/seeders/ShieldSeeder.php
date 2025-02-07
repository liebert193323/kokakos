<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_bill","view_any_bill","create_bill","update_bill","restore_bill","restore_any_bill","replicate_bill","reorder_bill","delete_bill","delete_any_bill","force_delete_bill","force_delete_any_bill","view_complaint","view_any_complaint","create_complaint","update_complaint","restore_complaint","restore_any_complaint","replicate_complaint","reorder_complaint","delete_complaint","delete_any_complaint","force_delete_complaint","force_delete_any_complaint","view_income","view_any_income","create_income","update_income","restore_income","restore_any_income","replicate_income","reorder_income","delete_income","delete_any_income","force_delete_income","force_delete_any_income","view_payment","view_any_payment","create_payment","update_payment","restore_payment","restore_any_payment","replicate_payment","reorder_payment","delete_payment","delete_any_payment","force_delete_payment","force_delete_any_payment","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_room","view_any_room","create_room","update_room","restore_room","restore_any_room","replicate_room","reorder_room","delete_room","delete_any_room","force_delete_room","force_delete_any_room","view_tenant","view_any_tenant","create_tenant","update_tenant","restore_tenant","restore_any_tenant","replicate_tenant","reorder_tenant","delete_tenant","delete_any_tenant","force_delete_tenant","force_delete_any_tenant","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","widget_StatsOverview","view_complaint::handler","view_any_complaint::handler","create_complaint::handler","update_complaint::handler","restore_complaint::handler","restore_any_complaint::handler","replicate_complaint::handler","reorder_complaint::handler","delete_complaint::handler","delete_any_complaint::handler","force_delete_complaint::handler","force_delete_any_complaint::handler","view_shield::role","view_any_shield::role","create_shield::role","update_shield::role","delete_shield::role","delete_any_shield::role"]},{"name":"penghuni","guard_name":"web","permissions":["view_complaint","view_any_complaint","create_complaint","update_complaint","restore_complaint","restore_any_complaint","replicate_complaint","reorder_complaint","delete_complaint","delete_any_complaint","force_delete_complaint","force_delete_any_complaint","view_payment","view_any_payment","create_payment","update_payment","restore_payment","restore_any_payment","replicate_payment","reorder_payment","delete_payment","delete_any_payment","force_delete_payment","force_delete_any_payment"]}]';
        $directPermissions = '{"24":{"name":"view_complaint::manager","guard_name":"web"},"25":{"name":"view_any_complaint::manager","guard_name":"web"},"26":{"name":"create_complaint::manager","guard_name":"web"},"27":{"name":"update_complaint::manager","guard_name":"web"},"28":{"name":"restore_complaint::manager","guard_name":"web"},"29":{"name":"restore_any_complaint::manager","guard_name":"web"},"30":{"name":"replicate_complaint::manager","guard_name":"web"},"31":{"name":"reorder_complaint::manager","guard_name":"web"},"32":{"name":"delete_complaint::manager","guard_name":"web"},"33":{"name":"delete_any_complaint::manager","guard_name":"web"},"34":{"name":"force_delete_complaint::manager","guard_name":"web"},"35":{"name":"force_delete_any_complaint::manager","guard_name":"web"},"103":{"name":"view_pengaduan","guard_name":"web"},"104":{"name":"view_any_pengaduan","guard_name":"web"},"105":{"name":"create_pengaduan","guard_name":"web"},"106":{"name":"update_pengaduan","guard_name":"web"},"107":{"name":"restore_pengaduan","guard_name":"web"},"108":{"name":"restore_any_pengaduan","guard_name":"web"},"109":{"name":"replicate_pengaduan","guard_name":"web"},"110":{"name":"reorder_pengaduan","guard_name":"web"},"111":{"name":"delete_pengaduan","guard_name":"web"},"112":{"name":"delete_any_pengaduan","guard_name":"web"},"113":{"name":"force_delete_pengaduan","guard_name":"web"},"114":{"name":"force_delete_any_pengaduan","guard_name":"web"},"115":{"name":"view_pengaduan::admin","guard_name":"web"},"116":{"name":"view_any_pengaduan::admin","guard_name":"web"},"117":{"name":"create_pengaduan::admin","guard_name":"web"},"118":{"name":"update_pengaduan::admin","guard_name":"web"},"119":{"name":"restore_pengaduan::admin","guard_name":"web"},"120":{"name":"restore_any_pengaduan::admin","guard_name":"web"},"121":{"name":"replicate_pengaduan::admin","guard_name":"web"},"122":{"name":"reorder_pengaduan::admin","guard_name":"web"},"123":{"name":"delete_pengaduan::admin","guard_name":"web"},"124":{"name":"delete_any_pengaduan::admin","guard_name":"web"},"125":{"name":"force_delete_pengaduan::admin","guard_name":"web"},"126":{"name":"force_delete_any_pengaduan::admin","guard_name":"web"},"127":{"name":"view_admin::complaint","guard_name":"web"},"128":{"name":"view_any_admin::complaint","guard_name":"web"},"129":{"name":"create_admin::complaint","guard_name":"web"},"130":{"name":"update_admin::complaint","guard_name":"web"},"131":{"name":"restore_admin::complaint","guard_name":"web"},"132":{"name":"restore_any_admin::complaint","guard_name":"web"},"133":{"name":"replicate_admin::complaint","guard_name":"web"},"134":{"name":"reorder_admin::complaint","guard_name":"web"},"135":{"name":"delete_admin::complaint","guard_name":"web"},"136":{"name":"delete_any_admin::complaint","guard_name":"web"},"137":{"name":"force_delete_admin::complaint","guard_name":"web"},"138":{"name":"force_delete_any_admin::complaint","guard_name":"web"},"139":{"name":"view_user::complaint","guard_name":"web"},"140":{"name":"view_any_user::complaint","guard_name":"web"},"141":{"name":"create_user::complaint","guard_name":"web"},"142":{"name":"update_user::complaint","guard_name":"web"},"143":{"name":"restore_user::complaint","guard_name":"web"},"144":{"name":"restore_any_user::complaint","guard_name":"web"},"145":{"name":"replicate_user::complaint","guard_name":"web"},"146":{"name":"reorder_user::complaint","guard_name":"web"},"147":{"name":"delete_user::complaint","guard_name":"web"},"148":{"name":"delete_any_user::complaint","guard_name":"web"},"149":{"name":"force_delete_user::complaint","guard_name":"web"},"150":{"name":"force_delete_any_user::complaint","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
