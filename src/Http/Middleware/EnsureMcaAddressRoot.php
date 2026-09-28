<?php

namespace Mca\Address\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mca\Permission\Services\PackageAccessService;
use Mca\Permission\Services\PermissionService;
use Symfony\Component\HttpFoundation\Response;

class EnsureMcaAddressRoot
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $forbidden = function_exists('mca_addr') ? mca_addr('errors.root_only') : 'Bu MCA paketi için yetkiniz yok.';

        if (class_exists(PackageAccessService::class)
            && is_array(config('permission.packages.address'))) {
            $packages = app(PackageAccessService::class);
            $ability = $packages->abilityForRequest($request);

            if ($packages->allows($user, 'address', $ability)) {
                return $next($request);
            }

            abort(403, $forbidden);
        }

        if (config('address.access.use_permission_root', true) && class_exists(PermissionService::class)) {
            if (app(PermissionService::class)->isRoot($user)) {
                return $next($request);
            }

            abort(403, $forbidden);
        }

        $column = (string) config('address.access.role_column', 'role_id');
        $rootRole = (string) config('address.access.root_role', 'root');
        $value = $user->{$column} ?? null;

        if ($column === 'role_id' && $value && class_exists(\Mca\Permission\Models\Role::class)) {
            if (\Mca\Permission\Models\Role::query()->whereKey($value)->where('is_root', true)->exists()) {
                return $next($request);
            }
        } elseif ((string) $value === $rootRole) {
            return $next($request);
        }

        abort(403, $forbidden);
    }
}
